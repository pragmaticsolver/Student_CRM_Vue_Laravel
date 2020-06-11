<?php

namespace Database\Seeds\Custom;

use Illuminate\Database\Seeder;
use App\Settings;

class Seeder1589607897 extends Seeder
{
    public function run()
    {
        $settingsToSeed = [
            'lesson_description_required' => 0,
            'lesson_objectives_required' => 0,
            'lesson_fulltext_required' => 0,
            'lesson_thumbnail_required' => 0,
            'lesson_video_required' => 0,
            'student_lesson_prep_required' => 0,
            'vocab_list_required' => 0,
            'extra_materials_text_required' => 0,
            'lesson_teachers_notes_required' => 0,
            'lesson_teachers_prep_required' => 0,
            'exercises_required' => 0,
            'homework_required' => 0,
            'downloadable_files_required' => 0,
            'pdf_files_required' => 0,
            'audio_files_required' => 0,
        ];
        foreach($settingsToSeed as $key => $value){
            $setting = new Settings();
            $setting->name = $key;
            $setting->value = $value;
            $setting->save();
        }
        
    }
}