<?php

namespace App\Http\Controllers;

use App\ClassUsage;
use App\Helpers\AutomatedTagsHelper;
use App\Helpers\LineHelper;
use App\MonthlyPayments;
use App\Settings;
use App\ZoomMeeting;
use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Event\AccountLinkEvent;

class ApiController extends Controller
{
    public function stripeWebhook()
    {
        \Stripe\Stripe::setApiKey(Settings::get_value('stripe_secret_key'));
        $endpoint_secret = Settings::get_value('stripe_webhook_signing_secret_key');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $data = $event->data->object;
                $stripe_invoice_id = $data->id;
                $paid_at = $data->status_transitions->paid_at;

                $payment = MonthlyPayments::where('stripe_invoice_id', $stripe_invoice_id)->first();
                if($payment)
                {
                    $payment->status = 'paid';
                    $payment->payment_recieved_at =  \Carbon\Carbon::createFromTimestampUTC($paid_at)->format('Y-m-d H:i:s');
                    $payment->save();
                    ClassUsage::paymentPaid($payment);

                    $automatedTagsHelper = new AutomatedTagsHelper($payment->student);
                    $automatedTagsHelper->refreshOutsandingPaymentTag();
                }
                break;
        }
        
        http_response_code(200);
    }

    public function zoomWebhook(Request $request)
    {
        // Validate request
        $verification_token = Settings::get_value('zoom_webhook_verification_token');
        $req_verification_token = $request->header('authorization');
        if($req_verification_token != $verification_token)
        {
            http_response_code(400);
            exit();
        }

        $payload = @file_get_contents('php://input');
        $data = json_decode($payload,1);

        if($data['event'] == 'meeting.deleted') {

            $meeting_id = $data['payload']['object']['id'];
            ZoomMeeting::where('id', $meeting_id)->delete();

        } else if($data['event'] == 'meeting.updated') {
            
            $meeting_id = $data['payload']['object']['id'];
            $zoomMeeting = ZoomMeeting::find($meeting_id);
            if($zoomMeeting) {
                $zoomMeeting->updatedFromZoom($data['payload']['object']);
            }
        }

        http_response_code(200);
    }

    public function lineWebhook(Request $request)
    {
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        if (!$signature) {
            return response('Bad Request', 400);
        }

        $res = LineHelper::getBot();
        if($res['status'] == 0) {
            \Log::error($res['message']);
            response('Internal Server Error', 500);
        }
        $bot = $res['bot'];

        // Check request with signature and parse request
        try {
            $events = $bot->parseEventRequest($request->getContent(), $signature);
        } catch (InvalidSignatureException $e) {
            return response('Invalid signature', 400);
        } catch (InvalidEventRequestException $e) {
            return response('Invalid event request', 400);
        }

        foreach ($events as $event) {
            if ($event instanceof FollowEvent) {
                LineHelper::handleFollowEvent($bot, $event);
            }
            else if ($event instanceof UnfollowEvent) {
                LineHelper::handleUnFollowEvent($bot, $event);
            }
            else if ($event instanceof AccountLinkEvent) {
                LineHelper::handleAccountLinkEvent($bot, $event);
            }
        }

        return response('',200);
    }
}
