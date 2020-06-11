<?php

namespace App;

use App\Settings;
use Carbon\Carbon;
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Students extends Model
{
    protected $table = 'students';
    
  	protected $fillable = [
  	    'user_id',
    	'firstname',
    	'lastname',
    	'firstname_kanji',
    	'lastname_kanji',
    	'firstname_furigana',
    	'lastname_furigana',
    	'status',
    	'join_date',
    	'home_phone',
    	'mobile_phone',
    	'email',
    	'address',
    	'toiawase_referral',
    	'toiawase_memo',
    	'toiawase_getter',
    	'toiawase_houhou',
    	'toiawase_date',
    	'teacher_id',
    	'birthday',
    	'comment',
        'levels',
        'rfid_token',
        'office_name',
        'office_address',
        'office_phone',
        'school_name',
        'school_address',
        'school_phone',
        'guardian1_name',
        'guardian1_address',
        'guardian1_phone',
        'guardian2_name',
        'guardian2_address',
        'guardian2_phone'
  	];

    public function book_students() {
        return $this->hasMany('App\BookStudents', 'student_id', 'id');
    }

    public function yoyakus() {
        return $this->hasMany('App\Yoyaku', 'customer_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function parent_user()
    {
        return $this->belongsTo('App\User', 'parent_user_id', 'id');
    }

    public function student_tests()
    {
        return $this->hasMany('App\StudentTests', 'student_id', 'id');
    }

    public function student_paper_tests()
    {
        return $this->hasMany('App\StudentPaperTests', 'student_id', 'id');
    }

    public function monthly_payments()
    {
        return $this->hasMany('App\MonthlyPayments', 'customer_id', 'id')->where('payment_category',NULL)->orderBy('period','DESC');
    }

    public function oneoff_payments()
    {
        return $this->hasMany('App\MonthlyPayments', 'customer_id', 'id')->where('payment_category','!=',NULL)->orderBy('date','DESC');
    }

    public function payments()
    {
        return $this->hasMany('App\MonthlyPayments', 'customer_id', 'id')->orderBy('payment_recieved_at','DESC')->orderBy('id','DESC');
    }

    /**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;

  	public function get_kanji_name() {
  	    return $this->firstname_kanji.$this->lastname_kanji;
    }

    public function getFullNameAttribute()
    {
        $locale = \App::getLocale();
        if($locale == 'ja')
        {
            return $this->firstname_kanji.' '.$this->lastname_kanji . ' ('.$this->firstname.' '.$this->lastname.')';
        }
        else
        {
            return $this->firstname.' '.$this->lastname;
        }
    }

    public function getfullNameForEmail($locale)
    {
        if($locale == 'ja')
        {
            return $this->lastname_kanji.' '.$this->firstname_kanji;
        }
        else
        {
            return $this->firstname.' '.$this->lastname;
        }
    }

    /**
     * Get all the students who checked-in this book.
     *
     * @return mixed
     */
    public function books() {
        return $this->belongsToMany('App\Books', 'books_checkin', 'student_id', 'book_id');
    }

    public static function createByUser($user)
    {
        $date = Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d');
        self::create([
            'user_id'=> $user->id,
            'lastname' => '',
            'firstname' => $user->name,
            'lastname_kanji' => '',
            'firstname_kanji' => $user->name,
            'lastname_furigana' => '',
            'firstname_furigana' => '',
            'home_phone' => '',
            'mobile_phone' => '',
            'email' => $user->email,
            'status'=> 1,
            'join_date' => $date,
            'address' => '',
            'toiawase_referral' => '',
            'toiawase_memo' => '',
            'toiawase_getter' => '',
            'toiawase_houhou' => '',
            'toiawase_date' => $date,
            'teacher_id' => 0,
            'birthday' => $date,
            'comment' => ''
        ]);
    }

    // fetch lat long from address string and save it to db.
    public function updateAddressLatLong()
    {
        if(!$this->address)
        {
            $this->addr_latitude = NULL;
            $this->addr_longitude = NULL;
            $this->save();
            return;
        }

        $key = Settings::get_value('google_map_api_key');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?key=".$key."&address=".urlencode($this->address);
        $json_reponse = file_get_contents($url);
        $res = json_decode($json_reponse,1);

        if($res['status'] == "OK" &&
            isset($res['results'][0]['geometry']['location']['lat']) &&
            isset(($res['results'][0]['geometry']['location']['lng']))
        )
        {
            $this->addr_latitude = $res['results'][0]['geometry']['location']['lat'];
            $this->addr_longitude = $res['results'][0]['geometry']['location']['lng'];
            $this->save();
        }
        else
        {
            $this->addr_latitude = NULL;
            $this->addr_longitude = NULL;
            $this->save();
            Log::error('error while fetching lat long for url '.$url.' response => '.$json_reponse);
        }
    }

    public static function all_student_todo_alert_count($date)
    {
        return \DB::table('todo_accesses')
                ->join('todos','todos.id','=','todo_accesses.todo_id')
                ->where('todo_accesses.student_id', '!=', NULL)
                ->where('todos.start_alert_before_days','!=',NULL)
                ->whereRaw("'".$date."'".' >= DATE_SUB(IFNULL(todo_accesses.custom_due_date,todo_accesses.due_date), INTERVAL todos.start_alert_before_days DAY)')
                ->whereRaw('(SELECT COUNT(*) from todo_tasks where todo_id = todos.id ) > (SELECT COUNT(*) from todo_task_statuses where todo_access_id = todo_accesses.id AND status = 1)')
                ->count();
    }

    public function todo_alert_count($date)
    {
        return \DB::table('todo_accesses')
                ->join('todos','todos.id','=','todo_accesses.todo_id')
                ->where('todo_accesses.student_id', $this->id)
                ->where('todos.start_alert_before_days','!=',NULL)
                ->whereRaw("'".$date."'".' >= DATE_SUB(IFNULL(todo_accesses.custom_due_date,todo_accesses.due_date), INTERVAL todos.start_alert_before_days DAY)')
                ->whereRaw('(SELECT COUNT(*) from todo_tasks where todo_id = todos.id ) > (SELECT COUNT(*) from todo_task_statuses where todo_access_id = todo_accesses.id AND status = 1)')
                ->count();
    }

    public function todoAccess()
    {
        return $this->hasMany('App\TodoAccess','student_id','id');
    }

    public function todo_assigned_count()
    {
        return $this->todoAccess()->count();
    }

    public function todo_completed_count()
    {
        return $this->todoAccess()
                ->whereRaw('(SELECT COUNT(*) from todo_tasks where todo_id = todo_accesses.todo_id ) = (SELECT COUNT(*) from todo_task_statuses where todo_access_id = todo_accesses.id AND status = 1)')
                ->count();
    }

    public function willUseParentEmail()
    {
        if($this->parent_user_id)
        {
            return true;
        }

        return false;
    }

    public function getEmailAddress()
    {
        if($this->willUseParentEmail())
        {
            return $this->parent_user->email;
        }

        return $this->user->email;
    }

    public function uploadedImageDetails()
    {
        if($this->image && Storage::disk('public')->has('students/'.$this->image))
        {
            $out = array();
            $out['upload']['filename'] = $this->image;
            $out['name'] = basename($this->image);
            $out['size'] = Storage::disk('public')->size('students/'.$this->image);
            $out['url'] = Storage::disk('public')->url('students/'.$this->image);
            return json_encode($out);
        }
        else
        {
            return '';
        }
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag','student_tags', 'student_id')->withPivot('detail_params')->orderBy('is_automated','DESC')->orderBy('id','ASC');
    }

    public function getTags()
    {
        $out = [];
        foreach($this->tags as $tag){
            $name = $tag->name;
            if($tag->pivot->detail_params)
            {
                $params = json_decode($tag->pivot->detail_params,1);
                if(isset($params['birth_date']))
                {
                    $name = $params['birth_date'];
                }
            }
            $temp = [
                'id' => $tag->id,
                'color' => $tag->color,
                'name' => $name,
                'icon' => $tag->icon,
                'is_automated' => $tag->is_automated
            ];
            $out[] = $temp;
        }
        return $out;
    }

    public function getImageUrl()
    {
        return Storage::disk('public')->url('students/'.$this->image);
    }

    public function paymentSetting()
    {
        return $this->hasOne('App\PaymentSetting', 'student_id', 'id');
    }

    public function getPaymentSettings()
    {
        if($this->paymentSetting)
        {
            return $this->paymentSetting;
        }
        else
        {
            return new PaymentSetting();
        }
    }

    public function isArchived()
    {
        return $this->user->hasRole(Role::ARCHIVED_STUDENT);
    }

    public function archive()
    {
        $student = $this;
        $student->user->roles()->detach();
        $student->user->assignRole(Role::ARCHIVED_STUDENT);
    }
}
