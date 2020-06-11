<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Classes;

class Events extends Model
{
    protected $table = 'classes';

    public $timestamps = false;

    protected $fillable = [
        'title', 'class_type', 'description', 'cost', 'size', 'category_id', 'level'
    ];

    public static function all($columns = ['*']) {
        return Classes::where('class_type', '1')->get();
    }

    public static function find($event_id) {
        return Classes::where('id', $event_id)->first();
    }
}
