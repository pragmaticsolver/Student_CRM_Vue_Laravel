<?php

namespace Database\Seeds\Custom;

use App\Settings;
use Illuminate\Database\Seeder;

class Seeder1588757870 extends Seeder
{
    public function run()
    {
        $settingsToSeed = [
            'use_line_messaging_api' => 0,
            'use_login_with_line' => 0,
        ];
        foreach($settingsToSeed as $key => $value){
            $setting = new Settings();
            $setting->name = $key;
            $setting->value = $value;
            $setting->save();
        }
    }
}