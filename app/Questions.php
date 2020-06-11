<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    protected $table = 'questions';
    
    protected $fillable = [
        'question', 'score', 'test_id'
    ];

    public $timestamps = false;

    public function test()
    {
        return $this->belongsTo('App\Tests', 'test_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany('App\Answers', 'question_id', 'id');
    }

    public static function get_store_validate_params()
    {
        return [
            'question' => 'required|max:191',
            'test_id' => 'required',
            'score' => 'required|numeric'
        ];
    }

    public static function get_update_validate_params()
    {
        return [
            'question' => 'required|max:191',
            'score' => 'required|numeric'
        ];
    }
}
