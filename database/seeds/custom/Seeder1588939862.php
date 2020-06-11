<?php

namespace Database\Seeds\Custom;

use App\Permission;
use Illuminate\Database\Seeder;

class Seeder1588939862 extends Seeder
{
    public function run()
    {
        // Permissions
        $permissionsToSeed = array(
            'line-settings',
            'notification-settings'
        );
        foreach($permissionsToSeed as $permission) {
            Permission::create([ 'name' => $permission ]);
        }
    }
}