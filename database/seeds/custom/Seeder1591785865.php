<?php

namespace Database\Seeds\Custom;

use Illuminate\Database\Seeder;
use App\Category;

class Seeder1591785865 extends Seeder
{
    public function run()
    {
        $categoriesToSeed = array(
            'class',
            'contact',
            'payment',
            'schedule',
            'student',
            'teacher',
            'to-do',
            'settings',
            'management',
            'availability',
            'attendance',
            'assessment',
            'logs',
            'others'
        );

        foreach($categoriesToSeed as $category) {
            Category::create([ 'name' => $category ]);
        }
    }
}