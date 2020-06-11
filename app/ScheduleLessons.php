<?php

namespace App;

use App\Helpers\CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ScheduleLessons extends Model
{
    protected $table = 'schedule_lessons';
    
    protected $fillable = [
        'schedule_id', 'lesson_id', 'schedule_unit_id', 'date', 'complete'
    ];

    public $timestamps = false;

    public function schedule()
    {
        return $this->belongsTo('App\Schedules', 'schedule_id', 'id');
    }

    public function lesson()
    {
        return $this->belongsTo('App\Lessons', 'lesson_id', 'id');
    }

    public function get_valid_yoyakus()
    {
        $schedule = $this->schedule;
        $valid_yoyakus = collect();

        if ($schedule->registered_students()->count() > 0) {
            $student_ids = $schedule->get_student_ids();

            foreach ($student_ids as $id) {
                $yoyaku = $schedule->yoyaku()->where('customer_id', $id)->orderBy('date', 'desc')->first();

                if($this->check_valid_yoyaku($yoyaku)) $valid_yoyakus->push($yoyaku);
            }
        }

        return $valid_yoyakus;
    }

    /**
     * Check if the current yoyaku is a valid yoyaku.
     * Valid yoyaku is a yoyaku still attend to the schedule.
     * Return true if the lesson's complete date is less than yoyaku last attend date.
     * Return true if the schedule is a once off class.
     * Return false if the yoyaku is waitlist.
     *
     * @params \App\ScheduleLessons $schedule_lesson
     * @params \App\Yoyaku $yoyaku
     *
     * @return boolean.
     */
    public function check_valid_yoyaku($yoyaku)
    {
        $schedule = $this->schedule;

        if($schedule->type == 1) return true;
        if($yoyaku->waitlist) return false;

        return $this->date < $yoyaku->date ? true : false;
    }

    public function commentUpdatedByUser()
    {
        return $this->hasOne('App\User','id','comment_updated_by');
    }

    public function getLocalCommentUpdatedAt()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->comment_updated_at, 'UTC')->setTimezone(CommonHelper::getSchoolTimezone());
    }

    public function getCommentStatusLine()
    {
        $updated_at = $this->getLocalCommentUpdatedAt()->format('Y/m/d H:i');
        $updated_by = "";
        if($this->commentUpdatedByUser)
        {
            $updated_by = $this->commentUpdatedByUser->name;
        }

        $status_line = __('messages.saved-by').' '.$updated_by.' at '.$updated_at;
        return $status_line;
    }
}
