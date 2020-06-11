<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Settings;

class Lessons extends Model
{
    protected $table = 'lessons';
    
    protected $fillable = [
    	'course_id', 'title', 'description', 'full_text',
        'thumbnail', 'unit_id', 
        'objectives', 'position',
        'student_lesson_prep','vocab_list','extra_materials_text','teachers_notes','teachers_prep'
  	];

    public $timestamps = false;

    public function course()
    {
        return $this->belongsTo('App\Courses', 'course_id', 'id');
    }

    public function unit() {
        return $this->belongsTo('App\Units', 'unit_id', 'id');
    }

    public function tests()
    {
        return $this->hasMany('App\Tests', 'lesson_id', 'id');
    }

    public function paper_tests()
    {
        return $this->hasMany('App\PaperTests', 'lesson_id', 'id');
    }

    public function assessment_lessons()
    {
        return $this->hasMany('App\AssessmentLessons', 'lesson_id', 'id');
    }

    public function assessments()
    {
        return $this->belongsToMany('App\Assessments', 'assessment_lessons', 'lesson_id', 'assessment_id');
    }

    public function schedule_lessons()
    {
        return $this->hasMany('App\Lessons', 'lesson_id', 'id');
    }

    public function get_image()
    {
        return Storage::disk('public')->url('lesson/thumbnail/'.$this->thumbnail);
    }

    public function the_image()
    {
        return '<img src="'.$this->get_image().'" width=100 height=100>';
    }

    public function the_downloadable_files_url()
    {
        $html = '<div class="files-list">';
        foreach($this->downloadableFiles as $lessonFile)
        {
            $html.= "<a href='".htmlspecialchars(Storage::disk('public')->url($lessonFile->file_path),ENT_QUOTES)."' download>".(empty($lessonFile->file_name) ? basename($lessonFile->file_path) : $lessonFile->file_name)."</a><button data-type='lesson' data-id='".$lessonFile->id."' data-name='".(empty($lessonFile->file_name) ? basename($lessonFile->file_path) : $lessonFile->file_name)."' class='btn btn-defualt btn_file_name_edit'><i class='fa fa-pencil'></i></button><br>";
        }
        return $html."</div>";
    }

    public function the_pdf_files_url()
    {
        $html = '<div class="files-list">';
        foreach($this->pdfFiles as $lessonFile)
        {
            $html.= "<a href='".htmlspecialchars(Storage::disk('public')->url($lessonFile->file_path),ENT_QUOTES)."' target='_blank'>".(empty($lessonFile->file_name) ? basename($lessonFile->file_path) : $lessonFile->file_name)."</a><button data-type='lesson' data-id='".$lessonFile->id."' data-name='".(empty($lessonFile->file_name) ? basename($lessonFile->file_path) : $lessonFile->file_name)."' class='btn btn-defualt btn_file_name_edit'><i class='fa fa-pencil'></i></button><br>";
        }
        return $html."</div>";
    }

    public function the_audio_files_url()
    {
        $html = '<div class="files-list">';
        foreach($this->audioFiles as $lessonFile)
        {
            $html.=  "<div><audio controls><source src='".htmlspecialchars(Storage::disk('public')->url($lessonFile->file_path),ENT_QUOTES)."'></audio></div>";
            $html.= "<div><a href='".htmlspecialchars(Storage::disk('public')->url($lessonFile->file_path),ENT_QUOTES)."' download>".(empty($lessonFile->file_name) ? basename($lessonFile->file_path) : $lessonFile->file_name)."</a><button data-type='lesson' data-id='".$lessonFile->id."' data-name='".(empty($lessonFile->file_name) ? basename($lessonFile->file_path) : $lessonFile->file_name)."' class='btn btn-defualt btn_file_name_edit'><i class='fa fa-pencil'></i></button></div>";
            $html.=  "<br>";
        }
        return $html."</div>";
    }

    public function the_extramaterial_files_url()
    {
        $html = '<div class="files-list">';
        foreach($this->extraMaterialFiles as $lessonFile)
        {
            $html.= "<a href='".htmlspecialchars(Storage::disk('public')->url($lessonFile->file_path),ENT_QUOTES)."' download>".(empty($lessonFile->file_name) ? basename($lessonFile->file_path) : $lessonFile->file_name)."</a><button data-type='lesson' data-id='".$lessonFile->id."' data-name='".(empty($lessonFile->file_name) ? basename($lessonFile->file_path) : $lessonFile->file_name)."'  class='btn btn-defualt btn_file_name_edit'><i class='fa fa-pencil'></i></button><br>";
        }
        return $html."</div>";
    }

    public function the_video_url()
    {
        $html = '<div class="files-list">';
        foreach($this->videoFiles as $lessonFile)
        {
            $html.= "<button id=\"playme\" onclick=\"revealVideo('video-lightbox','youtube','".$lessonFile->file_path."')\">".(empty($lessonFile->file_name) ? ($lessonFile->file_path) : $lessonFile->file_name)."</button><button data-type='lesson' data-id='".$lessonFile->id."' data-name='".(empty($lessonFile->file_name) ? basename($lessonFile->file_path) : $lessonFile->file_name)."'  class='btn btn-defualt btn_file_name_edit'><i class='fa fa-pencil'></i></button><br>";
        }
        return $html."</div>";
    }

    public static function get_store_validate_params()
    {
        return [
            'title'=>'required',
            'course_id'=>'required',
            'unit_id' => 'required',
            'description' => Settings::get_value('lesson_description_required') ? 'required' : 'nullable',
            'objectives' => Settings::get_value('lesson_objectives_required') ? 'required' : 'nullable',
            'full_text' => Settings::get_value('lesson_fulltext_required') ? 'required' : 'nullable',
            'image' => Settings::get_value('lesson_thumbnail_required') ? 'required' : 'nullable',
            'student_lesson_prep' => Settings::get_value('student_lesson_prep_required') ? 'required' : 'nullable',
            'vocab_list' => Settings::get_value('vocab_list_required') ? 'required' : 'nullable',
            'extra_materials_text' => Settings::get_value('extra_materials_text_required') ? 'required' : 'nullable',
            'teachers_notes' => Settings::get_value('lesson_teachers_notes_required') ? 'required' : 'nullable',
            'teachers_prep' => Settings::get_value('lesson_teachers_prep_required') ? 'required' : 'nullable',
        ];
    }

    public static function get_update_validate_params()
    {
        return [
            'title' => 'required',
            'description' => Settings::get_value('lesson_description_required') ? 'required' : 'nullable',
            'objectives' => Settings::get_value('lesson_objectives_required') ? 'required' : 'nullable',
            'full_text' => Settings::get_value('lesson_fulltext_required') ? 'required' : 'nullable',
            'image' => Settings::get_value('lesson_thumbnail_required') ? 'required' : 'nullable',
            'student_lesson_prep' => Settings::get_value('student_lesson_prep_required') ? 'required' : 'nullable',
            'vocab_list' => Settings::get_value('vocab_list_required') ? 'required' : 'nullable',
            'extra_materials_text' => Settings::get_value('extra_materials_text_required') ? 'required' : 'nullable',
            'teachers_notes' => Settings::get_value('lesson_teachers_notes_required') ? 'required' : 'nullable',
            'teachers_prep' => Settings::get_value('lesson_teachers_prep_required') ? 'required' : 'nullable',
            'exercise_titles' => Settings::get_value('exercises_required') ? 'required' : 'nullable',
            'homework_titles' => Settings::get_value('homework_required') ? 'required' : 'nullable',
            'video_links' => Settings::get_value('lesson_video_required') ? 'required' : 'nullable',
        ];
    }

    public function files()
    {
        return $this->hasMany('App\LessonFile','lesson_id','id');
    }

    public function downloadableFiles()
    {
        return $this->hasMany('App\LessonFile','lesson_id','id')->where('section',1);
    }

    public function pdfFiles()
    {
        return $this->hasMany('App\LessonFile','lesson_id','id')->where('section',2);
    }

    public function audioFiles()
    {
        return $this->hasMany('App\LessonFile','lesson_id','id')->where('section',3);
    }

    public function extraMaterialFiles()
    {
        return $this->hasMany('App\LessonFile','lesson_id','id')->where('section',4);
    }

    public function videoFiles()
    {
        return $this->hasMany('App\LessonFile','lesson_id','id')->where('section',5);
    }
    public function lessonVideoJson()
    {
        return json_encode($this->videoFiles);
    }

    public function downloadableFilesForDropzone()
    {
        $out = array();
        foreach($this->downloadableFiles as $file){
            $out[] = [
                'id' => $file->id,
                'name' => basename($file->file_path)
            ];
        }
        return json_encode($out);
    }

    public function pdfFilesForDropzone()
    {
        $out = array();
        foreach($this->pdfFiles as $file){
            $out[] = [
                'id' => $file->id,
                'name' => basename($file->file_path)
            ];
        }
        return json_encode($out);
    }

    public function audioFilesForDropzone()
    {
        $out = array();
        foreach($this->audioFiles as $file){
            $out[] = [
                'id' => $file->id,
                'name' => basename($file->file_path)
            ];
        }
        return json_encode($out);
    }

    public function extraMaterialFilesForDropzone()
    {
        $out = array();
        foreach($this->extraMaterialFiles as $file){
            $out[] = [
                'id' => $file->id,
                'name' => basename($file->file_path)
            ];
        }
        return json_encode($out);
    }

    public function lessonExercises()
    {
        return $this->hasMany('App\LessonExercise', 'lesson_id', 'id');
    }

    public function lessonExercisesJson()
    {
        return json_encode($this->lessonExercises);
    }

    public function lessonHomeworks()
    {
        return $this->hasMany('App\LessonHomework', 'lesson_id', 'id');
    }

    public function lessonHomeworksJson()
    {
        return json_encode($this->lessonHomeworks);
    }
}
