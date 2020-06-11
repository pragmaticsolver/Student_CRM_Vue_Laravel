<?php

namespace Database\Seeds\Custom;

use Illuminate\Database\Seeder;
use App\Settings;

class Seeder1589467189 extends Seeder
{
    public function run()
    {
        $settingsToSeed = [
            'lesson_description' => 1,
            'lesson_objectives' => 1,
            'lesson_fulltext' => 1,
            'lesson_thumbnail' => 1
        ];
        foreach($settingsToSeed as $key => $value){
            $setting = new Settings();
            $setting->name = $key;
            $setting->value = $value;
            $setting->save();
        }
    }
}