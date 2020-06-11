<?php

namespace Database\Seeds\Custom;

use Illuminate\Database\Seeder;
use App\Role;

class Seeder1590216743 extends Seeder
{
    public function run()
    {
        $roleHasPermissionsToSeed = array(
            array('role' => 'Super Admin','permission' => 'terminal-settings'),
            array('role' => 'Super Admin','permission' => 'lesson-settings'),
        );
        foreach($roleHasPermissionsToSeed as $record) {
            $role = Role::where('name', $record['role'])->first();
            $role->givePermissionTo($record['permission']);
        }
    }
}