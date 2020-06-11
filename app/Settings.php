<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';
    
  	protected $fillable = [
    	'name',
    	'value'
  	];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;

    /**
     * Get the default limit number of student per class.
     *
     * @return mixed
     */
  	public static function limitStudentNumber() {
  	    return self::where('name', 'limit_number_of_students_per_class')->first()->value;
    }

    public static function get_value($name) {
  	    $settings = self::where('name', $name);
  	    if($settings->count() == 0) throw new \Exception(__('messages.emptysetting', ['setting' => $name]));

        $setting = $settings->first();
  	    return $setting->value;
    }

    public static function update_value($name, $value) {
        $settings = self::where('name', $name);
        if($settings->count() == 0) throw new \Exception(__('messages.emptysetting', ['setting' => $name]));

        $setting = $settings->first();
        $setting->value = $value;
        $setting->save();
    }
}
