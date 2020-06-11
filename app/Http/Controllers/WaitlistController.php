<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Helpers\ScheduleHelper;
use App\Yoyaku;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function reserve_watitlist($id, Request $request)
    {
        $yoyaku = Yoyaku::findOrFail(decrypt($id));
        CommonHelper::setLocalByYoyaku($yoyaku);

        return view('email_actions.reserve-waitlist',[
            'yoyaku' => $yoyaku,
            'id' => $id
        ]);
    }

    public function reserve_waitlist_submit($id, Request $request)
    {
        $yoyaku = Yoyaku::findOrFail(decrypt($id));
        CommonHelper::setLocalByYoyaku($yoyaku);

        $schedule = $yoyaku->schedule;
        $student = $yoyaku->student;
        $date = $yoyaku->date;
        $activityByUser = $currentUser = $student->user;
        $res = ScheduleHelper::makeReservation($schedule, $student, $date, $activityByUser, $currentUser, $yoyaku->taiken);

        if($res['success'] == false)
        {
            $with['error'] =  $res['error'];
        }
        else
        {
            $with['success'] = $res['message'];

            // Delete waitlist if successfully registered for class
            $yoyaku->delete();
        }

        return redirect(route('statuspage', app()->getLocale()))->with($with);
    }

    public function cancel_waitlist($id, Request $request)
    {
        $yoyaku = Yoyaku::findOrFail(decrypt($id));
        CommonHelper::setLocalByYoyaku($yoyaku);

        return view('email_actions.cancel-waitlist',[
            'yoyaku' => $yoyaku,
            'id' => $id
        ]);
    }

    public function cancel_waitlist_submit($id, Request $request)
    {
        $yoyaku = Yoyaku::findOrFail(decrypt($id));
        CommonHelper::setLocalByYoyaku($yoyaku);

        $yoyaku->delete();
        $with['success'] = __('messages.you-are-successfully-removed-from-waitlist');

        return redirect(route('statuspage', app()->getLocale()))->with($with);
    }

    public function status_page($lang)
    {
        if($lang)
        {
            app()->setLocale($lang);
        }
        return view('email_actions.status');
    }
}
