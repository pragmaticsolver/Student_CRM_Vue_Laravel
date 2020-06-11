<?php

namespace App;

use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TodoTaskNote extends Model
{
    protected $table = 'todo_task_notes';
    
    public function updatedByUser()
    {
        return $this->hasOne('App\User','id','updated_by');
    }

    public function getLocalUpdatedAt()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->updated_at, 'UTC')->setTimezone(CommonHelper::getSchoolTimezone());
    }
}
