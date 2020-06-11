<?php

namespace Database\Seeds\Custom;

use Illuminate\Database\Seeder;
use App\Settings;

class Seeder1589533942 extends Seeder
{
    public function run()
    {
        $settingsToSeed = [
            'class_reminder' => 1,
            'event_reminder' => 1,
        ];
        foreach($settingsToSeed as $key => $value){
            $setting = new Settings();
            $setting->name = $key;
            $setting->value = $value;
            $setting->save();
        }   
    }
}