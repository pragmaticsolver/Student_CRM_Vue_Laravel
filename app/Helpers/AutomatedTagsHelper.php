<?php

namespace App\Helpers;

use App\Settings;
use App\Tag;
use Carbon\Carbon;

class AutomatedTagsHelper
{
    var $student;
    var $tag_ids;

    public function __construct($student)
    {
        $this->student = $student;

        $tags = Tag::OnlyAutomated()->get();
        foreach($tags as $tag){
            $tag_ids[$tag->name] = $tag->id;
        }
        $this->tag_ids = $tag_ids;
    }

    public function getTagIdByName($tag_name)
    {
        return $this->tag_ids[$tag_name];
    }

    public function refreshUpcommingBirthdayTag()
    {
        $attach = 0;
        $birthdate = $this->student->birthday;
        if($birthdate)
        {
            $timezone = CommonHelper::getSchoolTimezone();
            $today = Carbon::now($timezone)->startOfDay();
            $year = $today->format('Y');
            $bithday_this_year = Carbon::createFromFormat('Y-m-d', $birthdate, $timezone)->startOfDay()->year($year);

            $birth_date = (clone $bithday_this_year)->format('jS M');

            $diff_in_days = $today->diffInDays($bithday_this_year, false);
            if($diff_in_days >= 0 && $diff_in_days <= 7)
            {
                $attach = 1;
            }
        }

        if($attach)
        {
            $detail_params = json_encode([
                'birth_date' => $birth_date
            ]);
            $this->student->tags()->syncWithoutDetaching([ $this->getTagIdByName(Tag::UPCOMMING_BIRTHDAY) => ['detail_params' => $detail_params ] ]);
        }
        else
        {
            $this->student->tags()->detach($this->getTagIdByName(Tag::UPCOMMING_BIRTHDAY));
        }
    }

    public function refreshNewStudentTag()
    {
        $attach = 0;
        $join_date = $this->student->join_date;
        if($join_date)
        {
            $timezone = CommonHelper::getSchoolTimezone();
            $today = Carbon::now($timezone)->startOfDay();
            $join_date_carbon = Carbon::createFromFormat('Y-m-d', $join_date, $timezone)->startOfDay();

            $diff_in_days = $join_date_carbon->diffInDays($today, false);
            if($diff_in_days >= 0 && $diff_in_days <= Settings::get_value('new_student_tag_attachment_duration_days'))
            {
                $attach = 1;
            }
        }
        
        if($attach)
        {
            $this->student->tags()->syncWithoutDetaching([$this->getTagIdByName(Tag::NEW_STUDENT)]);
        }
        else
        {
            $this->student->tags()->detach($this->getTagIdByName(Tag::NEW_STUDENT));
        }
    }

    public function refreshDueTodoTag()
    {
        $attach = 0;

        $timezone = CommonHelper::getSchoolTimezone();
        $date = Carbon::now($timezone)->startOfDay()->format('Y-m-d');
        $due_todos_count = $this->student->todo_alert_count($date);
        if($due_todos_count > 0)
        {
            $attach = 1;
        }

        if($attach)
        {
            $this->student->tags()->syncWithoutDetaching([$this->getTagIdByName(Tag::DUE_TODO)]);
        }
        else
        {
            $this->student->tags()->detach($this->getTagIdByName(Tag::DUE_TODO));
        }
    }

    public function refreshLongTimeStudentTag()
    {
        $detach = [
            $this->getTagIdByName(Tag::LONG_TIME_STUDENT_1),
            $this->getTagIdByName(Tag::LONG_TIME_STUDENT_2),
            $this->getTagIdByName(Tag::LONG_TIME_STUDENT_3),
            $this->getTagIdByName(Tag::LONG_TIME_STUDENT_4),
            $this->getTagIdByName(Tag::LONG_TIME_STUDENT_5)
        ];
        $attach_id = NULL;

        $timezone = CommonHelper::getSchoolTimezone();
        $today = Carbon::now($timezone)->startOfDay();
        $join_date = $this->student->join_date;

        if($join_date)
        {
            $join_date_carbon = Carbon::createFromFormat('Y-m-d', $join_date, $timezone)->startOfDay();
            $diff_in_years = $join_date_carbon->diffInYears($today, false);

            if($diff_in_years >= 5)
            {
                $key = array_search($this->getTagIdByName(Tag::LONG_TIME_STUDENT_5), $detach);
                unset($detach[$key]);
                $attach_id = $this->getTagIdByName(Tag::LONG_TIME_STUDENT_5);
            }
            else if($diff_in_years >= 4)
            {
                $key = array_search($this->getTagIdByName(Tag::LONG_TIME_STUDENT_4), $detach);
                unset($detach[$key]);
                $attach_id = $this->getTagIdByName(Tag::LONG_TIME_STUDENT_4);
            }
            else if($diff_in_years >= 3)
            {
                $key = array_search($this->getTagIdByName(Tag::LONG_TIME_STUDENT_3), $detach);
                unset($detach[$key]);
                $attach_id = $this->getTagIdByName(Tag::LONG_TIME_STUDENT_3);
            }
            else if($diff_in_years >= 2)
            {
                $key = array_search($this->getTagIdByName(Tag::LONG_TIME_STUDENT_2), $detach);
                unset($detach[$key]);
                $attach_id = $this->getTagIdByName(Tag::LONG_TIME_STUDENT_2);
            }
            else if($diff_in_years >= 1)
            {
                $key = array_search($this->getTagIdByName(Tag::LONG_TIME_STUDENT_1), $detach);
                unset($detach[$key]);
                $attach_id = $this->getTagIdByName(Tag::LONG_TIME_STUDENT_1);
            }
        }

        if($attach_id)
        {
            $this->student->tags()->syncWithoutDetaching($attach_id);
        }
        $this->student->tags()->detach($detach);
    }

    public function refreshOutsandingPaymentTag()
    {
        $attach = 0;

        $exists = $this->student->payments()->where('rest_month',0)->where('status','!=','paid')->exists();
        if($exists)
        {
            $attach = 1;
        }

        if($attach)
        {
            $this->student->tags()->syncWithoutDetaching([ $this->getTagIdByName(Tag::OUTSTANDING_PAYMENT) ]);
        }
        else
        {
            $this->student->tags()->detach($this->getTagIdByName(Tag::OUTSTANDING_PAYMENT));
        }
    }

    public function refreshRFIDRegisteredTag()
    {
        $attach = 0;

        if($this->student->rfid_token)
        {
            $attach = 1;
        }

        if($attach)
        {
            $this->student->tags()->syncWithoutDetaching([ $this->getTagIdByName(Tag::RFID_REGISTERED) ]);
        }
        else
        {
            $this->student->tags()->detach($this->getTagIdByName(Tag::RFID_REGISTERED));
        }
    }

    public function refreshLineConnectedTag()
    {
        $attach = 0;

        if($this->student->user->line_user_id)
        {
            $attach = 1;
        }

        if($attach)
        {
            $this->student->tags()->syncWithoutDetaching([ $this->getTagIdByName(Tag::LINE_CONNECTED) ]);
        }
        else
        {
            $this->student->tags()->detach($this->getTagIdByName(Tag::LINE_CONNECTED));
        }
    }
}