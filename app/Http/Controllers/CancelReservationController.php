<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityEnum;
use App\Helpers\CommonHelper;
use App\Schedules;
use App\Yoyaku;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CancelReservationController extends Controller
{
    public function index($id)
    {
        $yoyaku = Yoyaku::find(decrypt($id));
        CommonHelper::setLocalByYoyaku($yoyaku);

        if(!$this->is_cancellable($yoyaku))
        {
            $with['error'] =  __('messages.reservation-is-not-in-cancellable-state');
            return redirect(route('statuspage', app()->getLocale()))->with($with);
        }

        return view('cancel_reservation.index',[
            'yoyaku' => $yoyaku,
            'id' => $id
        ]);
    }

    public function cancelReservation(Request $request)
    {
        $id = decrypt($request->id);
        $yoyaku = Yoyaku::find($id);
        CommonHelper::setLocalByYoyaku($yoyaku);

        $with = [];
        if($this->is_cancellable($yoyaku))
        {
            $res = CommonHelper::cancelReservation([$yoyaku],"cancel", $yoyaku->student->user->id, ActivityEnum::RESERVERATION_CANCELLED_VIA_EMAIL);
            if($res['error'] != '')
            {
                $with['error'] = $res['error'];
            }
            else
            {
                $with['success'] = __('messages.reservation-cancelled-successfully');
            }
        }
        else
        {
            $with['error'] =  __('messages.reservation-is-not-in-cancellable-state');
        }
        return redirect(route('statuspage', app()->getLocale()))->with($with);
    }

    private function is_cancellable($yoyaku)
    {
        $is_cancellable = 0;

        if(!$yoyaku)
        {
            return $is_cancellable;
        }

        // all day events are not cancellable (via email link)
        if($yoyaku->schedule->type == Schedules::EVENT_ALLDAY_TYPE)
        {
            return $is_cancellable;
        }

        $timezone = CommonHelper::getSchoolTimezone();
        $now = Carbon::now($timezone);
        $eventTime = Carbon::createFromFormat('Y-m-d H:i:s',$yoyaku->date.' '.$yoyaku->schedule->start_time,$timezone);

        if($now->lessThan($eventTime) && $yoyaku->status == 0)
        {
            $is_cancellable = 1;
        }
        return $is_cancellable;
    }
}
