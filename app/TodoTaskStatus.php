<?php

namespace App;

use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TodoTaskStatus extends Model
{
    protected $table = 'todo_task_statuses';
    
    public $fillable = [
        'todo_access_id',
        'todo_task_id'
    ];

    public function todoAccess()
    {
        return $this->belongsTo('App\TodoAccess', 'todo_access_id' ,'id');
    }

    public function updatedByUser()
    {
        return $this->hasOne('App\User','id','updated_by');
    }

    public function getLocalUpdatedAt()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->updated_at, 'UTC')->setTimezone(CommonHelper::getSchoolTimezone());
    }
}
