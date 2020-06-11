<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolOffDays extends Model
{
    protected $table = 'school_off_days';
    
  	protected $fillable = [
    	'date',
  	];

  	/**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;
}