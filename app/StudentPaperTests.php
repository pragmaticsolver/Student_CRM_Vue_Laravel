<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StudentPaperTests extends Model
{
    protected $table = 'student_paper_tests';
    
    protected $fillable = [
        'student_id', 'schedule_id', 'paper_test_id', 'score', 'date', 'comment_en', 'comment_ja', 'total_score', 'file_path'
    ];

    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo('App\Students', 'student_id', 'id');
    }

    public function schedule()
    {
        return $this->belongsTo('App\Schedules', 'schedule_id', 'id');
    }

    public function paper_test()
    {
        return $this->belongsTo('App\PaperTests', 'paper_test_id', 'id');
    }

    public function get_score()
    {
        return $this->score.'/'.$this->total_score;
    }

    public function dropzonePDFFile()
    {
        $out = array();
        $out['name'] = basename($this->file_path);
        $out['size'] = Storage::disk('public')->size($this->file_path);
        return json_encode($out);
    }

    public static function deleteWithFiles($ids)
    {
        $file_paths = StudentPaperTests::whereIn('id', $ids)->pluck('file_path')->toArray();
        foreach($file_paths as $file_path)
        {
            @Storage::disk('public')->delete($file_path);
        }
        StudentPaperTests::whereIn('id', $ids)->delete();
    }
}
