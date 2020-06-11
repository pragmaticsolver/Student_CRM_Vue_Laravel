<?php

namespace App;

use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    protected $table = 'contacts';
    
  	protected $fillable = [
    	'customer_id',
    	'message',
    	'status',
    	'type',
    	'user_id',
    	'date'
  	];

  	/**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo('App\Students', 'customer_id', 'id');
    }

    public function getLocalDate()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->date, 'UTC')->setTimezone(CommonHelper::getSchoolTimezone())->format('Y-m-d H:i:s');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
