<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentTemplates extends Model
{
    protected $table = 'comment_templates';
    
    private $student_name = '';
    private $class_name = '';
    private $test = '';
    private $score = '';
    private $date = '';
    private $comment = '';
    private $format_fields = [
        'student_name', 'class_name', 'test', 'score', 'date', 'comment'
    ];

    protected $fillable = [
        'name', 'content_en', 'content_ja'
    ];

    public $timestamps = false;

    public static function get_validate_params()
    {
        return [
            'name' => 'required|max:191',
            'content_en' => 'required',
            'content_ja' => 'required'
        ];
    }

    public function set_student_name($name = '')
    {
        $this->student_name = $name;
        return $this;
    }

    public function set_date($date = '')
    {
        $this->date = $date;
        return $this;
    }

    public function set_score($score = '')
    {
        $this->score = $score;
        return $this;
    }

    public function set_test($test = '')
    {
        $this->test = $test;
        return $this;
    }

    public function set_class_name($class_name = '')
    {
        $this->class_name = $class_name;
        return $this;
    }

    public function set_comment($comment = '')
    {
        $this->comment = $comment;
        return $this;
    }

    public function get_format($field)
    {
        $format_content = $this->$field;

        foreach($this->format_fields as $field) {
            if(strpos($this->$field, '{'.$field.'}') != -1) {
                $format_content = str_replace('{'.$field.'}', $this->$field, $format_content);
            }
        }

        return $format_content;
    }
}
