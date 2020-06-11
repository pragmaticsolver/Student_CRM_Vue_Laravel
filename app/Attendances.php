<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendances extends Model
{
    protected $table = 'attendances';
    
  	protected $fillable = [
    	'customer_id',
    	'yoyaku_id',
    	'teacher_id',
    	'payment_plan_id',
    	'class_id',
    	'schedule_id',
    	'status',
    	'date',
        'cancel_policy_id',
        'start_date',
        'end_date'
  	];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;

    public function yoyaku()
    {
        return $this->hasOne('App\Yoyaku', 'id','yoyaku_id');
    }

    public function schedule()
    {
        return $this->hasOne('App\Schedules', 'id', 'schedule_id');
    }

    public function cancellationPolicy()
    {
        return $this->hasOne('App\CancellationPolicies', 'id', 'cancel_policy_id');
    }
}
