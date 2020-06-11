<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Schedules;
use App\Settings;
use DB;

class Classes extends Model
{
    protected $table = 'classes';
    
    public const CLASS_TYPE = 0;
    public const EVENT_TYPE = 1;

  	protected $fillable = [
    	'title',
    	'payment_plan_id',
    	'status', 'class_type',
        'level', 'category_id',
        'size',
        'length',
        'default_course_id'
  	];

    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo('App\ClassCategories', 'category_id', 'id');
    }

    public function getFullDates($scheduleId) {
        // $yoyakus = Schedules::find($scheduleId)->yoyaku()->where('status', '!=', 2)->where('waitlist', '=', 0)->get();
        $yoyakus = DB::table('yoyakus')->select('yoyakus.schedule_id', 'yoyakus.date')->join('students','yoyakus.customer_id','students.id')->where('yoyakus.schedule_id','=',$scheduleId)->where('yoyakus.status', '!=', 2)->where('yoyakus.waitlist', '=', 0)->get();
        $countDates = Yoyaku::countDate($yoyakus);
        $limit = $this->size ? $this->size : Settings::limitStudentNumber();
        $fullDates = [];
        foreach($countDates as $date => $studentNumber) {
            if($studentNumber >= (int)$limit) array_push($fullDates, $date);
        }
        return $fullDates;
    }

    public function getSize()
    {
        return $this->size ? $this->size : Settings::get_value('limit_number_of_students_per_class');
    }

    public static function allClasses()
    {
        return self::where('class_type', '0')->get();
    }

    public function payment_plan()
    {
        return $this->belongsTo('App\PaymentPlans', 'payment_plan_id', 'id');
    }
}
