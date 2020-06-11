<?php

namespace App\Helpers;

use App\ActivityLog;
use App\Schedules;

class ActivityLogHelper {

    public static function create($activity_id, $user_id, $detail_params)
    {
        $activityLog = new ActivityLog();
        $activityLog->activity_id = $activity_id;
        $activityLog->user_id = $user_id;
        $activityLog->detail_params = is_array($detail_params) ? json_encode($detail_params) : NULL;
        $activityLog->created_at = date('Y-m-d H:i:s');
        $activityLog->save();
    }

    public static function getReservationMadeParams($yoyaku, $user_id)
    {
        $out = [];

        if($user_id != $yoyaku->student->user->id)
        {
            $out['Student'] = $yoyaku->student->firstname.' '.$yoyaku->student->lastname;
        }
        $key = $yoyaku->schedule->is_class() ? 'Class' : ($yoyaku->schedule->is_event() ? 'Event' : '');
        $out[$key] = $yoyaku->schedule->class->title;
        $out['Date'] = ( $yoyaku->start_date && $yoyaku->end_date ) ? $yoyaku->start_date.' - '.$yoyaku->end_date : $yoyaku->date;

        if($yoyaku->schedule->type == Schedules::EVENT_ALLDAY_TYPE)
        {
            $time = 'All Day';
        }
        else
        {
            $time = $yoyaku->schedule->start_time.' - '.$yoyaku->schedule->end_time;
        }

        $out['Time'] = $time;
        return $out;
    }

    public static function getReservationCancelledParams($yoyaku, $user_id, $cancel_type)
    {
        $out = [];

        if($user_id != $yoyaku->student->user->id)
        {
            $out['Student'] = $yoyaku->student->firstname.' '.$yoyaku->student->lastname;
        }
        $out['Cancel Type'] = ucwords(str_replace("-"," ",$cancel_type));
        $key = $yoyaku->schedule->is_class() ? 'Class' : ($yoyaku->schedule->is_event() ? 'Event' : '');
        $out[$key] = $yoyaku->schedule->class->title;
        $out['Date'] = $yoyaku->date;

        if($yoyaku->schedule->type == Schedules::EVENT_ALLDAY_TYPE)
        {
            $time = 'All Day';
        }
        else
        {
            $time = $yoyaku->schedule->start_time.' - '.$yoyaku->schedule->end_time;
        }

        $out['Time'] = $time;
        return $out;
    }

    public static function getContactCreatedParams($contact)
    {
        $out = [];
        $out['Name'] = $contact->student->lastname_kanji.' '.$contact->student->firstname_kanji;
        $out['Type'] = $contact->type;
        $out['Memo'] = $contact->message;
        return $out;
    }

    public static function getTodoCreatedParams($todo)
    {
        $out = [];
        $out['Title'] = $todo->title;
        $out['No. of Tasks'] = $todo->todoTasks()->count();
        $out['No. of Assigned Users'] = $todo->todoAccess()->forUsers()->count();
        $out['No. of Assigned Students'] = $todo->todoAccess()->forStudents()->count();
        $out['Due Days'] = $todo->due_days;
        $out['Show Alert Before Days'] = $todo->start_alert_before_days ? $todo->start_alert_before_days : 0;
        $out['No. of Attachments'] = $todo->todoFiles()->count();
        return $out;
    }

    public static function getTodoCompletedParams($todoAccess)
    {
        $out = [];
        $out['Todo Title'] = $todoAccess->todo->title;
        if($todoAccess->student_id)
        {
            $out['Student'] = $todoAccess->student->firstname.' '.$todoAccess->student->lastname;
        }
        return $out;
    }

    public static function getClassScheduledParams($schedule)
    {
        $out = [];
        if($schedule->type == Schedules::CLASS_REPEATED_TYPE)
        {
            $out['Type'] = "Repeat Class";
            $out['Class'] = $schedule->class->title;
            $out['Start Date'] = $schedule->start_date;
            $out['End Date'] = $schedule->end_date;
            $out['Day of Week'] = $schedule->day_of_week;
        }
        else
        {
            $out['Type'] = "One Off Class";
            $out['Class'] = $schedule->class->title;
            $out['Date'] = $schedule->date;
        }
        $out['Time'] = $schedule->start_time.' - '.$schedule->end_time;
        $out['Teacher'] = $schedule->teacher->nickname;
        if($schedule->course_schedule)
        {
            $out['Course'] = $schedule->course_schedule->course->title;
        }

        return $out;
    }

    public static function getClassCancelledParams($schedule, $date)
    {
        $out = [];

        $out['Class'] = $schedule->class->title;
        $out['Cancel Date'] = $date;
        $out['Time'] = $schedule->start_time.' - '.$schedule->end_time;
        $out['Teacher'] = $schedule->teacher->nickname;
        if($schedule->course_schedule)
        {
            $out['Course'] = $schedule->course_schedule->course->title;
        }

        return $out;
    }

    public static function getReservationDeletedParams($yoyaku, $user_id)
    {
        $out = [];

        if($user_id != $yoyaku->student->user->id)
        {
            $out['Student'] = $yoyaku->student->firstname.' '.$yoyaku->student->lastname;
        }
        $key = $yoyaku->schedule->is_class() ? 'Class' : ($yoyaku->schedule->is_event() ? 'Event' : '');
        $out[$key] = $yoyaku->schedule->class->title;
        $out['Date'] = $yoyaku->date;

        if($yoyaku->schedule->type == Schedules::EVENT_ALLDAY_TYPE)
        {
            $time = 'All Day';
        }
        else
        {
            $time = $yoyaku->schedule->start_time.' - '.$yoyaku->schedule->end_time;
        }

        $out['Time'] = $time;
        return $out;
    }

    public static function getPaymentCUDParams($payment)
    {
        $out = [];
        $out['Id'] = $payment->id;
        $out['Student'] = $payment->student->firstname.' '.$payment->student->lastname;
        if($payment->rest_month)
        {
            $out['Payment Type'] = 'Monthly (Rest Month)';
            $out['Period'] = $payment->period;
        }
        elseif($payment->isOneOffPayment())
        {
            $out['Payment Type'] = 'Other';
            $out['Payment Category'] = $payment->payment_category;
            $out['Price'] = $payment->price;
            $out['Memo'] = $payment->memo;
            $out['Payment Method'] = $payment->display_payment_method ;
        }
        else
        {
            $out['Payment Type'] = 'Monthly';
            $out['Price'] = $payment->price;
            $out['Period'] = $payment->period;
            $out['Number of lessons'] = $payment->number_of_lessons;
            $out['Memo'] = $payment->memo;
            $out['Payment Method'] = $payment->display_payment_method ;
        }

       return $out;
    }
}
