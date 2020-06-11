<?php

namespace App;

use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LessonExerciseStatus extends Model
{
    protected $table = 'lesson_exercise_statuses';
    
    public function updatedByUser()
    {
        return $this->hasOne('App\User','id','updated_by');
    }

    public function getLocalUpdatedAt()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->updated_at, 'UTC')->setTimezone(CommonHelper::getSchoolTimezone());
    }

    public function getStatusLine()
    {
        $updated_at = $this->getLocalUpdatedAt()->format('Y/m/d H:i');
        $updated_by = "";
        if($this->updatedByUser)
        {
            $updated_by = $this->updatedByUser->name;
        }

        $status_line = ( $this->is_complete ?  __('messages.marked-as-complete-by') : __('messages.marked-as-incomplete-by') ).' '.$updated_by.' at '.$updated_at;

        return $status_line;
    }
}
