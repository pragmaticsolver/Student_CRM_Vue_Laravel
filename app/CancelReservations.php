<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CancelReservations extends Model
{
    protected $table = 'cancel_reservations';
    
    protected $fillable = [
    	'yoyaku_id',
    	'cancel_policy_id'
  	];

  	/**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;
}
