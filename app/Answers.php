<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answers extends Model
{
    protected $table = 'answers';
    
    protected $fillable = [
        'question_id', 'test_id', 'answer', 'order', 'correct'
    ];

    public $timestamps = false;

    public function question()
    {
        return $this->belongsTo('App\Questions', 'question_id', 'id');
    }

    public function test()
    {
        return $this->belongsTo('App\Tests', 'test_id', 'id');
    }

    public static function get_store_validate_params()
    {
        return [
            'question_id' => 'required',
            'test_id' => 'required',
            'order' => 'required|numeric|min:0',
            'answer' => 'required|max:191'
        ];
    }

    public static function get_update_validate_params()
    {
        return [
            'order' => 'required|numeric|min:0',
            'answer' => 'required|max:191'
        ];
    }
}
