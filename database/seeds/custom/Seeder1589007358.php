<?php

namespace Database\Seeds\Custom;

use App\Tag;
use Illuminate\Database\Seeder;

class Seeder1589007358 extends Seeder
{
    public function run()
    {
        $tagsToSeed = [
            [
                'name' => Tag::LINE_CONNECTED,
                'color' => "#5bba25",
                'icon' => "fa-info-circle",
                'is_automated' => 1,
            ]
        ];

        foreach ($tagsToSeed as $record) {
            $tag = new Tag();
            $tag->name = $record['name'];
            $tag->color = $record['color'];
            $tag->icon = $record['icon'];
            $tag->is_automated = $record['is_automated'];
            $tag->save();
        }
    }
}