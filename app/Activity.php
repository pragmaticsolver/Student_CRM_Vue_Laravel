<?php

namespace App;

use App\Helpers\ActivityEnum;
use Exception;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';

    public function get_display_name()
    {
        $key =  'messages.'.$this->name;
        return __($key);
    }
}
