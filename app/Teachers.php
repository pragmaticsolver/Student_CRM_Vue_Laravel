<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teachers extends Model
{
    protected $table = 'teachers';
    
  	protected $fillable = [
    	'name',
    	'nickname',
    	'furigana',
    	'profile',
    	'birthday',
    	'birthplace',
    	'status',
        'user_id',
        'color_coding',
        'username'
  	];
  	
  	/**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function schedules()
    {
        return $this->hasMany('App\Schedules', 'teacher_id', 'id');
    }

    public static function createByUser($user)
    {
        self::create([
            'name' => $user->name,
            'nickname' => $user->username,
            'username' => $user->username,
            'furigana' => $user->name,
            'profile'  => '',
            'birthplace' => '',
            'birthday' => now(),
            'status' => 0,
            'user_id' => $user->id,
        ]);
    }

    public function get_color_coding()
    {
        return $this->color_coding ? $this->color_coding : Settings::get_value('default_calendar_color_coding');
    }

    public function archive()
    {
        $teacher = $this;
        $teacher->user->roles()->detach();
        $teacher->user->assignRole(Role::ARCHIVED_TEACHER);

        $teacher->status = 1;
        $teacher->save();
    }
}
