<?php

namespace Database\Seeds\Custom;

use App\Settings;
use Illuminate\Database\Seeder;

class Seeder1588681835 extends Seeder
{
    public function run()
    {
        $settingsToSeed = [
            'line_login_channel_id' => NULL,
            'line_login_channel_secret' => NULL,
        ];
        foreach($settingsToSeed as $key => $value){
            $setting = new Settings();
            $setting->name = $key;
            $setting->value = $value;
            $setting->save();
        }
    }
}