<?php

namespace App\Helpers;

use App\Yoyaku;
use App\Classes;
use App\Settings;
use App\Students;
use App\Schedules;
use Carbon\Carbon;
use App\EmailTemplates;
use App\Mail\ReminderEmail;
use App\Helpers\ActivityEnum;
use App\Helpers\CommonHelper;
use App\Helpers\ActivityLogHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ScheduleHelper
{
    public static function makeReservation($schedule, $student, $date, $activityByUser, $currentUser, $taiken = 0)
    {
        $class = $schedule->class()->first();
        if($class->class_type == 1)
        {
            $res = ScheduleHelper::registerForEvent($currentUser, $schedule, $student, $date);
            if($res['success'])
            {
                ActivityLogHelper::create(
                    ActivityEnum::RESERVATION_MADE,
                    $activityByUser->id,
                    ActivityLogHelper::getReservationMadeParams($res['yoyaku'],$activityByUser->id)
                );
            }
            return $res;
        }
        else
        {
            // check if school off day
            $school_off_days = DB::table('school_off_days')->where('date','=',$date)->get()->first();
            if($school_off_days)
            {
                return ['success'=>false,'error'=>'School day off'];
            }

            // check if class is not already full
            $current_number_of_students = DB::table('yoyakus')->select('yoyakus.schedule_id')->join('students','yoyakus.customer_id','students.id')->where('yoyakus.schedule_id','=',$schedule->id)->where('yoyakus.date','=',$date)->where('yoyakus.status','<>',2)->where('yoyakus.waitlist','=',0)->get()->count();
            $limit = $class->size;
            if($limit == NULL)
            {
                $limit = Settings::get_value('limit_number_of_students_per_class');
            }
            if($current_number_of_students >= $limit)
            {
                return ['success'=>false,'error'=>'This class limit '. $limit . ' students!'];
            }

            // check if class is not past class
            if(!$schedule->isPastClassCheckPasses($currentUser,$date))
            {
                return ['success'=>false, 'error'=> __('messages.reservation-can-not-be-made-for-past-classes-or-events') ];
            }

            $dates_registered = array();
            $res = new Yoyaku();
            $res->customer_id = $student->id;
            $res->schedule_id = $schedule->id;
            $res->date = $date;
            $res->taiken = $taiken;
            $res->save();
            $dates_registered[] = $res->date;

            ActivityLogHelper::create(
                ActivityEnum::RESERVATION_MADE,
                $activityByUser->id,
                ActivityLogHelper::getReservationMadeParams($res,$activityByUser->id)
            );

            NotificationHelper::sendRegisterClassNotification($student, $schedule, $dates_registered);
            NotificationHelper::sendRegisterClassNotificationToTeacher($student, $schedule,$dates_registered);

            if($current_number_of_students + 1 == $limit)
            {
                $full = true;
            }
            else
            {
                $full = false;
            }

            $payments = DB::table('payments')->where('customer_id','=',$student->id)->orderby('date')->get();
            return ScheduleHelper::checkPayment($student->id, $payments, $schedule, $full);
        }
    }

    public static function registerForEvent($currentUser, $schedule, $student, $date)
    {
        if($schedule->check_full())
        {
            return ['success'=>false, 'error' => 'This Event is full!'];
        }

        if(!$schedule->isPastClassCheckPasses($currentUser,$date))
        {
            return[ 'success'=> false, 'error' => __('messages.reservation-can-not-be-made-for-past-classes-or-events') ];
        }

        $yoyaku = new Yoyaku([
            'customer_id' => $student->id,
            'schedule_id' => $schedule->id,
            'date' => $date,
            'taiken' => 0,
            'status' => 0,
        ]);

        if($yoyaku->save())
        {
            return [
                'success' => true,
                'message'=>'Event has been reserved!',
                'yoyaku' => $yoyaku,
                'class_type' => 'event',
                'student' => $yoyaku->student,
                'full' => $schedule->check_full(1)
            ];
        }

        return ['success'=>false, 'error' => 'Something went wrong'];
    }

    public static function checkPayment($customer_id, $payments, $schedule, $full, $yoyaku = NULL)
    {
        $use_points = Settings::get_value('use_points');
        $point_warning = 0;
        if($use_points == 'true')
        {
            if($payments->isEmpty())
            {
                $point_warning = 1;
            }
            else
            {
                $attendance_ids = array();
                $number_of_payments = count($payments);
                $count_payments = 0;
                $last_remaining_points = 0;
                $last_expiration_date = NULL;
                foreach ($payments as $payment) {
                    $count_payments ++;
                    $payment_expiration_date = $payment->expiration_date;
                    $remaining_points = $payment->points;
                    $attendances = DB::table('attendances')->select('attendances.id','payment_plans.points', 'attendances.date', 'attendances.cancel_policy_id')->join('classes','attendances.class_id','=','classes.id')->join('payment_plans','classes.payment_plan_id','=','payment_plans.id')->where('customer_id','=',$customer_id)->where('date','<=',$payment_expiration_date)->orderby('date')->get();
                    if(!$attendances->isEmpty()) {
                        if($last_expiration_date != NULL && $attendances[0]->date <= $last_expiration_date) {
                            $remaining_points += $last_remaining_points;
                        }
                        foreach ($attendances as $attendance) {
                            if($attendance->cancel_policy_id != NULL) {
                                $policy = DB::table('cancellation_policies')->where('id','=',$attendance->cancel_policy_id)->get()->first();
                                $attendance->points = $policy->points;
                            }
                            if(!in_array($attendance->id, $attendance_ids)) {
                                if($remaining_points >= $attendance->points) {
                                    $attendance_ids[] = $attendance->id;
                                    $remaining_points -= $attendance->points;
                                } else {
                                    $last_remaining_points = $remaining_points;
                                    $last_expiration_date = $payment_expiration_date;
                                }
                            }
                        }
                    }
                    if($count_payments == $number_of_payments) {
                        $attendances = DB::table('attendances')->select('attendances.id','payment_plans.points', 'attendances.date', 'attendances.cancel_policy_id')->join('classes','attendances.class_id','=','classes.id')->join('payment_plans','classes.payment_plan_id','=','payment_plans.id')->where('customer_id','=',$customer_id)->orderby('date')->get();
                        if(!$attendances->isEmpty()) {
                            if($attendances[0]->date > $payment_expiration_date) {
                                $remaining_points = 0;
                            }
                            foreach ($attendances as $attendance) {
                                if($attendance->cancel_policy_id != NULL) {
                                    $policy = DB::table('cancellation_policies')->where('id','=',$attendance->cancel_policy_id)->get()->first();
                                    $attendance->points = $policy->points;
                                }
                                if(!in_array($attendance->id, $attendance_ids)) {
                                    $attendance_ids[] = $attendance->id;
                                    $remaining_points -= $attendance->points;
                                }
                            }
                            $last_remaining_points = $remaining_points;
                            $last_expiration_date = $payment_expiration_date;
                        } else {
                            $date = Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d');
                            if($payment_expiration_date > $date) {
                                $last_remaining_points = $remaining_points;
                                $last_expiration_date = $payment_expiration_date;
                            }
                        }
                    }
                }

                $current_class = DB::table('schedules')->select('payment_plans.points')->join('classes','schedules.class_id','=','classes.id')->join('payment_plans','classes.payment_plan_id','=','payment_plans.id')->where('schedules.id','=',$schedule->id)->get()->first();
                $current_class_points = $current_class->points;

                if($current_class_points > $last_remaining_points)
                {
                    $point_warning = 1;
                }
            }
        }

        $out = array();
        $out['success'] = true;
        $out['message'] = 'Class has been reserved.!';
        if($point_warning == 1)
        {
            $out['warning'] = 'Warning: This student does not have points remaining!';
        }
        $out['full'] = $full;
        $out['yoyaku'] = $yoyaku;
        return $out;
    }
}
?>
