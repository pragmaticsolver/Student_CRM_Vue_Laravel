<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassesOffDays extends Model
{
    protected $table = 'classes_off_days';
    
    protected $fillable = [
    	'schedule_id',
    	'date'
  	];

  	/**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;
}
