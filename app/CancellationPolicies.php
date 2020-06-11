<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CancellationPolicies extends Model
{
    protected $table = 'cancellation_policies';
    
    protected $fillable = [
    	'cancel_type_id',
    	'payment_plan_id',
    	'points',
    	'salary'
  	];

  	/**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;

    public function cancelType()
    {
        return $this->hasOne('App\CancelType', 'id', 'cancel_type_id');
    }
}
