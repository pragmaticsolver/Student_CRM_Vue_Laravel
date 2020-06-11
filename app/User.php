<?php

namespace App;

use App\Helpers\AutomatedTagsHelper;
use App\Helpers\NotificationHelper;
use App\Notifications\VerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    protected $table = 'users';
    
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'lang', 'receive_emails', 'zoom_email', 'is_force_verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function assessment_users()
    {
        return $this->hasMany('App\AssessmentUsers', 'user_id', 'id');
    }

    public function student()
    {
        return $this->hasOne('App\Students', 'user_id', 'id');
    }

    public function teacher()
    {
        return $this->hasOne('App\Teachers', 'user_id', 'id');
    }

    public function hasVerifiedEmail()
    {
        if($this->willUseParentEmail())
        {
            return ! is_null($this->student->parent_user->email_verified_at);
        }

        return ! is_null($this->email_verified_at);
    }

    public function sendEmailVerificationNotification()
    {
        $user = $this;
        if($user->willUseParentEmail())
        {
            $user = $user->student->parent_user;
        }
        
        NotificationHelper::sendVerifyEmailNotification($user);
    }

    public function getEmailForVerification()
    {   
        if($this->willUseParentEmail())
        {
            return $this->student->parent_user->email;
        }

        return $this->email;
    }

    public static function getDefaultLanuage($role_name = "")
    {
        $role = NULL;
        if($role_name) {
            $role = Role::where('name', $role_name)->first();
        }

        if($role && $role->default_lang) {
            $lang = $role->default_lang;
        } else {
            $lang = Settings::get_value('default_lang');
        }

        return $lang;
    }

    public function get_lang()
    {
        return $this->lang;
    }

    public function getVerificationURL()
    {
        $verifyEmail = new VerifyEmail();
        return $verifyEmail->getVerificationURL($this);
    }

    public function get_calendar_view()
    {
        return $this->calendar_view ? $this->calendar_view : Settings::get_value('default_calendar_view');
    }

    public function get_role()
    {
        $user_role_name = $this->getRoleNames()->first();

        if($user_role_name) {
            $user_role = Role::findByName($user_role_name);

            return $user_role ? $user_role : null;
        } else {
            return null;
        }
    }

    public function my_todo_alert_count($date)
    {
        $res = \DB::table('todo_accesses')
                ->join('todos','todos.id','=','todo_accesses.todo_id')
                ->where('todo_accesses.user_id', $this->id)
                ->where('todos.start_alert_before_days','!=',NULL)
                ->whereRaw("'".$date."'".' >= DATE_SUB(IFNULL(todo_accesses.custom_due_date,todo_accesses.due_date), INTERVAL todos.start_alert_before_days DAY)')
                ->whereRaw('(SELECT COUNT(*) from todo_tasks where todo_id = todos.id ) > (SELECT COUNT(*) from todo_task_statuses where todo_access_id = todo_accesses.id AND status = 1)')
                ->count();
        return $res;
    }

    public function children()
    {
        return $this->hasMany('App\Students','parent_user_id','id');
    }

    public function getEmailAddress()
    {
        // can not call $this->student->getEmailAddress(); direclty without check
        // to return email of user even if user->student not exists.

        if($this->willUseParentEmail())
        {
            return $this->student->getEmailAddress();
        }

        return $this->email;
    }

    public function willUseParentEmail()
    {
        if(!$this->student)
        {
            return false;
        }

        return $this->student->willUseParentEmail();
    }

    public function getStripeCustomerId()
    {
        if(!$this->stripe_customer_id)
        {
            \Stripe\Stripe::setApiKey(Settings::get_value('stripe_secret_key'));

            $customer = \Stripe\Customer::create([
              'name' => $this->name,
              'email' => $this->getEmailAddress(),
            ]);

            $this->stripe_customer_id = $customer->id;
            $this->save();
        }

        return $this->stripe_customer_id;
    }

    public function check_can_login()
    {
        $role = $this->get_role();
        return $role->can_login;
    }

    public function setLineUserId($line_user_id)
    {
        $this->line_user_id = $line_user_id;
        $this->save();

        if($this->student) {
            $automatedTagsHelper = new AutomatedTagsHelper($this->student);
            $automatedTagsHelper->refreshLineConnectedTag();
        }
    }
}
