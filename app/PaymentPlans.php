<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentPlans extends Model
{
	protected $table = 'payment_plans';
	
  	protected $fillable = [
    	'cost',
    	'cost_to_teacher',
    	'points'
  	];

  	/**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;
}