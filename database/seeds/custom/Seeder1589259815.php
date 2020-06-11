<?php

namespace Database\Seeds\Custom;

use Illuminate\Database\Seeder;
use App\Settings;
use App\Permission;

class Seeder1589259815 extends Seeder
{
    public function run()
    {
        Permission::create([ 'name' => 'lesson-settings' ]);
        $settingsToSeed = [
            'lesson_video' => 1,
            'student_lesson_prep' => 1,
            'vocab_list' => 1,
            'extra_materials_text' => 1,
            'lesson_teachers_notes' => 1,
            'lesson_teachers_prep' => 1,
            'exercises' => 1,
            'homework' => 1,
            'downloadable_files' => 1,
            'pdf_files' => 1,
            'audio_files' => 1,
        ];
        foreach($settingsToSeed as $key => $value){
            $setting = new Settings();
            $setting->name = $key;
            $setting->value = $value;
            $setting->save();
        }

    }
}