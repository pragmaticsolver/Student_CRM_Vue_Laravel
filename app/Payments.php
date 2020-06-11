<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table = 'payments';
    
  	protected $fillable = [
    	'customer_id',
    	'points',
    	'price',
    	'date',
    	'expiration_date'
  	];

  	/**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo('App\Students', 'customer_id' , 'id');
    }
}
