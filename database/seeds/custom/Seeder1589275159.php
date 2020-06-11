<?php

namespace Database\Seeds\Custom;

use App\Permission;
use Illuminate\Database\Seeder;

class Seeder1589275159 extends Seeder
{
    public function run()
    {
        // Rename permission for existing instances
        $permission = Permission::where('name', 'manage-payment-batches')->first();
        if ($permission) {
            $permission->name = 'manage-monthly-payments';
            $permission->save();
        }
    }
}