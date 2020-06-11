<?php

namespace Database\Seeds\Custom;

use Illuminate\Database\Seeder;
use App\Settings;
use App\Permission;

class Seeder1589791164 extends Seeder
{
    public function run()
    {
        Permission::create([ 'name' => 'terminal-settings' ]);
        $settingsToSeed = [
            'terminal_checkin' => 1,
            'terminal_checkout' => 1,
            'terminal_reservation' => 1,
            'terminal_checkout_book' => 1,
        ];
        foreach($settingsToSeed as $key => $value){
            $setting = new Settings();
            $setting->name = $key;
            $setting->value = $value;
            $setting->save();
        }
    }
}