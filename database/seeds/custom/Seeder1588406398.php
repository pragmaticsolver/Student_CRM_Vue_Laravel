<?php

namespace Database\Seeds\Custom;

use App\Settings;
use Illuminate\Database\Seeder;

class Seeder1588406398 extends Seeder
{
    public function run()
    {
        $settingsToSeed = [
            'line_account_link_meesage_text' => 'Want to link your line account with your bce app account?',
            'line_account_link_meesage_button_text' => 'Link My Account',
            'line_account_linked_message_text_en' => 'Your line account is now linked with your bce app account!',
            'line_account_linked_message_text_ja' => 'これで、ラインアカウントがBCEアプリアカウントにリンクされました。',
        ];
        foreach($settingsToSeed as $key => $value){
            $setting = new Settings();
            $setting->name = $key;
            $setting->value = $value;
            $setting->save();
        }
    }
}