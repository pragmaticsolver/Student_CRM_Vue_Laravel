<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $table = 'todos';
    
    public function todoTasks()
    {
        return $this->hasMany('App\TodoTask','todo_id','id');
    }

    public function todoAccess()
    {
        return $this->hasMany('App\TodoAccess','todo_id','id');
    }

    public function todoFiles()
    {
        return $this->hasMany('App\TodoFile','todo_id','id');
    }
}
