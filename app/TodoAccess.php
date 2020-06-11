<?php

namespace App;

use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TodoAccess extends Model
{
    protected $table = 'todo_accesses';
    
    public $fillable = [
        'todo_id',
        'user_id',
        'student_id'
    ];

    public function todo()
    {
        return $this->belongsTo('App\Todo','todo_id','id');
    }

    public function todoTaskStatus()
    {
        return $this->hasMany('App\TodoTaskStatus','todo_access_id','id');
    }

    public function todoTaskStatusByTaskId()
    {
        return $this->todoTaskStatus->keyBy('todo_task_id');
    }

    public function todoTaskNote()
    {
        return $this->hasMany('App\TodoTaskNote','todo_access_id','id');
    }

    public function todoTaskNoteByTaskId()
    {
        return $this->todoTaskNote->keyBy('todo_task_id');
    }

    public function student()
    {
        return $this->hasOne('App\Students','id','student_id');
    }

    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function scopeforUsers($query)
    {
        $query->where('user_id','!=',NULL);
        $query->where('student_id',NULL);
    }

    public function scopeforStudents($query)
    {
        $query->where('user_id',NULL);
        $query->where('student_id','!=',NULL);
    }

    public function getIsCompletedAttribute()
    {
        return $this->todo->todoTasks()->count() == $this->todoTaskStatus()->where('status',1)->count() ? true : false;
    }

    public function getStatusAttribute()
    {
        $out = [];
        $out['status'] = '';
        $out['class'] = '';

        if($this->is_completed)
        {
            $out['status'] = __('messages.completed');
            $out['class'] = 'success';
            return $out;
        }

        if($this->todo->start_alert_before_days === NULL)
        {
            $out['status'] = '';
            $out['class'] = '';
            return $out;
        }

        $due_date = Carbon::createFromFormat('Y-m-d',$this->custom_due_date ? $this->custom_due_date : $this->due_date, CommonHelper::getSchoolTimezone())->endOfDay();
        $current_date = Carbon::now(CommonHelper::getSchoolTimezone());
        $start_alert_date = (clone $due_date)->subDays($this->todo->start_alert_before_days)->startOfDay();

        if((clone $current_date)->endOfDay() > $due_date)
        {
            $out['status'] = __('messages.due-passed');
            $out['class'] = 'danger';
            return $out;
        }

        if((clone $current_date)->startOfDay() >= $start_alert_date)
        {
            $out['status'] = __('messages.due-soon');
            $out['class'] = 'danger';
            return $out;
        }
        return false;
    }

    public function getLocalCreatedAt()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->created_at, 'UTC')->setTimezone(CommonHelper::getSchoolTimezone());
    }
}
