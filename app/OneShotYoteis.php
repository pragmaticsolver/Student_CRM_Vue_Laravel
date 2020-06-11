<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OneShotYoteis extends Model
{
	protected $table = 'one_shot_yoteis';
	
  	protected $fillable = [
    	'name',
    	'guest',
    	'date',
    	'start_time',
    	'end_time',
    	'teacher_id',
    	'status'
  	];

  	/**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;
}