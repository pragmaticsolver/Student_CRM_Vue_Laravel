<?php

namespace App;

use App\Helpers\CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ScheduleFile extends Model
{
    protected $table = 'schedule_files';
    
    protected $fillable = [
        'schedule_id', 'class_date', 'user_id', 'comment', 'file', 'file_name'
    ];

    public $timestamps = false;

    public function schedule()
    {
        return $this->belongsTo('App\Schedules', 'schedule_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    public function getAttachment()
    {
        $html = '<div class="files-list">';
        $html.= "<a href='".htmlspecialchars(Storage::disk('public')->url($this->file),ENT_QUOTES)."' target='_blank'>".(empty($this->file_name) ? basename($this->file) : $this->file_name)."</a><button data-type='schedule' data-id='".$this->id."' data-name='".(empty($this->file_name) ? basename($this->file) : $this->file_name)."' class='btn btn-defualt btn_file_name_edit'><i class='fa fa-pencil'></i></button><br>";
        return $html."</div>";
    }

    public function getLocalFileUpdatedAt()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->updated_at, 'UTC')->setTimezone(CommonHelper::getSchoolTimezone());
    }
}
