<?php

namespace App\Console\Commands;

use App\Settings;
use App\User;
use Illuminate\Console\Command;

class SpecialDeploymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onetime:special_deployment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run code specific to particular deployment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $default_lang = Settings::get_value('default_lang');

        $users = User::where('lang', NULL)->orWhere('lang','')->get();
        foreach($users as $user) 
        {
            $lang = $default_lang;
            
            $roles = $user->roles;
            if($roles->count() > 0) 
            {
                $role_lang = $roles[0]->default_lang;
                if($role_lang)
                {
                   $lang = $role_lang;
                }
            }
            
            $user->lang = $lang;
            $user->save();
        }
    }
}
