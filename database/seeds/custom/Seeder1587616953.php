<?php

namespace Database\Seeds\Custom;

use App\Settings;
use Illuminate\Database\Seeder;

class Seeder1587616953 extends Seeder
{
    public function run()
    {
        $settingsToSeed = [
            'line_channel_id' => NULL,
            'line_channel_secret' => NULL,
            'line_assertion_private_key' => NULL,
        ];
        foreach($settingsToSeed as $key => $value){
            $setting = new Settings();
            $setting->name = $key;
            $setting->value = $value;
            $setting->save();
        }
    }
}