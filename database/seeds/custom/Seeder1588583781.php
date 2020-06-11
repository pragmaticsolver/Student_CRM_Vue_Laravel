<?php

namespace Database\Seeds\Custom;

use Illuminate\Database\Seeder;
use App\Settings;

class Seeder1588583781 extends Seeder
{
    public function run()
    {
        $settingsToSeed = [
            'school_initial' => 'UT',
        ];
        foreach($settingsToSeed as $key => $value){
            $setting = new Settings();
            $setting->name = $key;
            $setting->value = $value;
            $setting->save();
        }

    }
}