<?php

namespace App\Helpers;

use \DB;
use App\Yoyaku;
use App\Settings;
use App\Students;
use App\Schedules;
use Carbon\Carbon;
use App\ClassUsage;
use App\Attendances;
use App\ClassesOffDays;
use App\Helpers\ScheduleHelper;
use App\Jobs\SendMail;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CommonHelper{

    public static function cancelReservation($yoyakus,$cancel_type, $user_id, $activity_type_id, $send_email = 1)
    {
        $out = array();
        $out['error'] = '';

        $user = User::find($user_id);
        $customer_id = $yoyakus[0]->customer_id;
        $schedule_id = $yoyakus[0]->schedule_id;
        $schedule = Schedules::find($schedule_id);
        $dates_cancelled = [];

        // This function is intended to delete multiple reservations of same student and same schedule at a same time.
        foreach($yoyakus as $yoyaku)
        {
            if(!($yoyaku->customer_id == $customer_id && $yoyaku->schedule_id == $schedule_id))
            {
                throw new \Exception('Cant cancel reseravtions of different users or diff schedules togeher');
            }
        }

        // Validate not cancelling past class/events
        $res = true;
        foreach($yoyakus as $yoyaku)
        {
            $res = $yoyaku->schedule->isPastClassCheckPasses($user, $yoyaku->date);
            if($res === false)
            {
                break;
            }
        }
        if($res === false)
        {
            $out['error'] = __('messages.reservation-can-not-be-cancelled-for-past-classes-or-events');
            return $out;
        }

        // Perform cancellation
        if($schedule->is_event())
        {
            foreach($yoyakus as $yoyaku)
            {
                ClassUsage::reservationCancelled($yoyaku);

                ActivityLogHelper::create(
                    $activity_type_id,
                    $user_id,
                    ActivityLogHelper::getReservationCancelledParams($yoyaku,$user_id,"simple-cancel")
                );

                $yoyaku->delete();
            }
        }
        else
        {
            foreach($yoyakus as $yoyaku)
            {
                $dates_cancelled[] = $yoyaku->date;
                $payment_plan_id = $yoyaku->schedule->class->payment_plan_id;

                $policy = DB::table('cancellation_policies')->select('cancellation_policies.id')->join('cancel_types','cancel_types.id','=','cancellation_policies.cancel_type_id')->where('cancel_types.alias','=',$cancel_type)->where('cancellation_policies.payment_plan_id','=',$payment_plan_id)->get()->first();
                if(!$policy) {
                    $default_policy = DB::table('cancellation_policies')->select('cancellation_policies.id')->join('cancel_types','cancel_types.id','=','cancellation_policies.cancel_type_id')->where('cancel_types.alias','=',$cancel_type)->where('cancellation_policies.payment_plan_id','=',0)->get()->first();
                    $policy_id = $default_policy->id;
                } else {
                    $policy_id = $policy->id;
                }

                $attendance = new Attendances([
                    'customer_id' => $yoyaku->customer_id,
                    'yoyaku_id' => $yoyaku->id,
                    'class_id' => $yoyaku->schedule->class_id ,
                    'teacher_id' => $yoyaku->schedule->teacher_id ,
                    'schedule_id' => $yoyaku->schedule_id,
                    'payment_plan_id' => $payment_plan_id,
                    'start_date' => $yoyaku->start_date,
                    'end_date' => $yoyaku->end_date,
                    'cancel_policy_id' => $policy_id,
                    'date' => Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d')
                ]);

                $attendance->save();

                if($yoyaku->taiken == '1')
                {
                    $student = Students::find($yoyaku->customer_id);
                    $student->status = 1;
                    $student->save();
                }

                $yoyaku->status = 2;
                $yoyaku->save();

                if($cancel_type == 'full-penalty-cancel')
                {
                    ClassUsage::ClassUsed($yoyaku);
                }
                else
                {
                    ClassUsage::reservationCancelled($yoyaku);
                }

                ActivityLogHelper::create(
                    $activity_type_id,
                    $user_id,
                    ActivityLogHelper::getReservationCancelledParams($yoyaku,$user_id,$cancel_type)
                );

                $yoyaku_date = $yoyaku->date;
                $yoyakus = Yoyaku::with('student.user','schedule.class')
                                        ->whereHas('student.user')
                                        ->whereHas('schedule.class')
                                        ->where('status','<>',2)
                                        ->where('waitlist',1)
                                        ->where('date', $yoyaku_date)
                                        ->where('schedule_id', $schedule_id)->get();

                if($send_email)
                {
                    if(!$yoyakus->isEmpty()) {
                        foreach ($yoyakus as $yoyaku) {
                            if($yoyaku->student->user->receive_emails == 1) {
                                NotificationHelper::sendCancelReservationNotifyWatlist($yoyaku);
                            }
                        }
                    }
                }
            }

            if($send_email)
            {
                NotificationHelper::sendCancelClassNotification($customer_id, $schedule_id, $dates_cancelled);
            }
        }
       
        return $out;
    }

    public static function deleteReservation($yoyaku, $user_id, $activity_type_id)
    {
        $yoyaku->delete();
        
        ActivityLogHelper::create(
            $activity_type_id,
            $user_id,
            ActivityLogHelper::getReservationDeletedParams($yoyaku,$user_id)
        );

        ClassUsage::reservationDeleted($yoyaku);
    }

    public static function cancelClass($schedule, $date, $user_id, $send_email = 1)
    {
        // Delete Zoom Meeting for class if exists
        $scheduleZoomMeeting = $schedule->getScheduleZoomMeeting($date);
        if($scheduleZoomMeeting)
        {
            $res = ZoomHelper::deleteZoomMeeting($scheduleZoomMeeting->zoom_meeting_id);
            if($res['status'] == 0) {
                throw new Exception($res['message']);
            }
        }
        
        // Delete Reservations for given class.
        $yoyakus = Yoyaku::where('date', $date)->where('schedule_id', $schedule->id)->get();
        $yoyaku_ids = [];
        foreach($yoyakus as $yoyaku){
            ClassUsage::reservationDeleted($yoyaku);
            // Send Email to student about class cancellation
            if($send_email)
            {
                if($yoyaku->status != 2 && $yoyaku->waitlist == 0)
                {
                    $customer_id = $yoyaku->customer_id;
                    $schedule_id = $yoyaku->schedule->id;
                    $dates_cancelled = [];
                    $dates_cancelled[] = $yoyaku->date;
                    NotificationHelper::sendCancelClassNotification($customer_id, $schedule_id, $dates_cancelled);
                }
            }
            $yoyaku_ids[] = $yoyaku->id;
        }
        if($yoyaku_ids)
        {
            Yoyaku::whereIn('id', $yoyaku_ids)->delete();
        }

        if($schedule->type == Schedules::CLASS_REPEATED_TYPE)
        {
            ClassesOffDays::firstOrCreate([
                'schedule_id' => $schedule->id,
                'date' => $date
            ]);
        }
        else
        {
            $schedule->delete();
        }

        ActivityLogHelper::create(
            ActivityEnum::CLASS_CANCELLED,
            $user_id,
            ActivityLogHelper::getClassCancelledParams($schedule, $date)
        );
    }

    public static function getSchoolTimezone()
    {
        // using static variable here to query actual value from db only once for multiple calls to this function during a request.
        static $timezone = "";
        if(!$timezone)
        {
            $timezone = Settings::get_value('school_timezone');
        }
        return $timezone;
    }

    public static function getMainLoggedInUserId()
    {
        $orig_user_id = Session::get('orig_user');
        if($orig_user_id)
        {
            return $orig_user_id;
        }
        else
        {
            return Auth::user()->id;
        }
    }

    public static function setLocalByYoyaku($yoyaku)
    {
        if(isset($yoyaku->student->user))
        {
            $lang = $yoyaku->student->user->get_lang();
            app()->setLocale($lang);
        }
    }

    public static function getFullcalendarCommonSettings()
    {
        $days = ['sun','mon','tue','wed','thu','fri','sat'];

        $out['timeZone'] = CommonHelper::getSchoolTimezone();

        $hiddenDays = [];
        $visible_days = Settings::get_value('working_days');
        if($visible_days)
        {
            $visible_days = explode(",", $visible_days);
            
            foreach($days as $i => $day){
                if(!in_array($day, $visible_days))
                {
                    $hiddenDays[] = $i;
                }
            }
        }
        $out['hiddenDays'] = $hiddenDays;


        $week_start_day = Settings::get_value('week_start_day');
        $firstDay = array_search($week_start_day, $days);
        if($firstDay < 0)
        {
            $firstDay = 0;
        }
        $out['firstDay'] = $firstDay;

        $default_show_calendar = explode(';', Settings::get_value('default_show_calendar'));
        $out['minTime'] = isset($default_show_calendar[0]) ? $default_show_calendar[0] : '00:00';
        $out['maxTime'] = isset($default_show_calendar[1]) ? $default_show_calendar[1] : '24:00';

        $out['locale'] = \Auth::user()->get_lang();
        return $out;
    }

    public static function base64UrlEncode($text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }
}


?>
