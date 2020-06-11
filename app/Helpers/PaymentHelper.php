<?php

namespace App\Helpers;

use App\MonthlyPayments;
use App\Settings;

class PaymentHelper {

    public static function createRestMonthPaymentRecord($customer_id, $period)
    {
        $payment = new MonthlyPayments();
        $payment->customer_id = $customer_id;
        $payment->status = 'draft';

        $payment->period = $period;

        $payment->rest_month = 1;
        $payment->number_of_lessons = 0;
        $payment->price = 0;
        $payment->payment_method = NULL;
        
        $payment->save();
        
        self::paymentCreated($payment);
        
        return $payment;
    }

    public static function createMonthlyPaymentRecord($customer_id, $price, $period, $number_of_lessons, $memo, $payment_method)
    {
        $payment = new MonthlyPayments();
        $payment->customer_id = $customer_id;
        $payment->status = 'draft';

        $payment->price = $price;
        $payment->period = $period;
        $payment->number_of_lessons = $number_of_lessons;
        $payment->memo = $memo;
        $payment->payment_method = $payment_method;

        $payment->save();

        self::paymentCreated($payment);
        
        return $payment;
    }

    public static function createOneoffPaymentRecord($customer_id, $payment_category, $price, $memo, $payment_method)
    {
        $payment = new MonthlyPayments();
        $payment->customer_id = $customer_id;
        $payment->status = 'draft';

        $payment->payment_category = $payment_category;
        $payment->price = $price;
        $payment->memo = $memo;
        $payment->payment_method = $payment_method;

        $payment->save();

        self::paymentCreated($payment);

        return $payment;
    }

    private static function paymentCreated($payment)
    {
        ActivityLogHelper::create(
            ActivityEnum::PAYMENT_CREATED,
            CommonHelper::getMainLoggedInUserId(),
            ActivityLogHelper::getPaymentCUDParams($payment)
        );

        $automatedTagsHelper = new AutomatedTagsHelper($payment->student);
        $automatedTagsHelper->refreshOutsandingPaymentTag();
    }

    public static function sendStripeInvoice($payment)
    {
        $stipe_invoice_item_description = "";
        if($payment->isOneOffPayment())
        {
            $stipe_invoice_item_description = $payment->payment_category;
        }
        else
        {
            $stipe_invoice_item_description = $payment->number_of_lessons.' Lesson(s) ( '. \Carbon\carbon::createFromFormat('Y-m', $payment->period)->format('F Y') .' )';
        }

        $user = $payment->student->user;
        
        try {
            $stripe_customer_id = $user->getStripeCustomerId();

            \Stripe\Stripe::setApiKey(Settings::get_value('stripe_secret_key'));

            \Stripe\InvoiceItem::create([
                'customer' => $stripe_customer_id,
                'currency' => 'JPY',
                'description' => $stipe_invoice_item_description,
                'amount' => $payment->price,
            ]);

            $invoice = \Stripe\Invoice::create([
                'customer' => $stripe_customer_id,
                'collection_method' => 'send_invoice',
                'description' => $payment->memo,
                'days_until_due' => 30
            ]);

            $invoice->sendInvoice();
        
            $payment->stripe_invoice_id = $invoice->id;
            $payment->stripe_invoice_url = $invoice->hosted_invoice_url;
            $payment->status = 'invoice-sent';
            $payment->save();
            return "";
        }
        catch(\Exception $e){
            $payment->status = 'stripe-error';
            $payment->save();

            return "Stripe Error: ".$e->getMessage();
        }
    }
}