<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\CommonHelper;
use App\Helpers\NotificationHelper;
use App\Http\Requests\UserRequest;
use App\Role;
use App\Settings;
use App\Students;
use App\Teachers;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:5', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'username' => $data['username'],
            'receive_emails' => $data['receive_emails']
        ]);
    }

    /**
     * @project: 2018112901_lut
     * Description: Load view register form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @auth: ChienKV - khuongchien@gmail.com
     * @throws \Exception
     * @version: 1.0
     */
    public function signup()
    {
        $role = Role::findByName(Settings::get_value('default_signup_role'));

        return view('auth.signup', [
            'class_student_levels' => explode(',', Settings::get_value('class_student_levels')),
            'signup_role' => isset($role->name) ? Str::slug(strtolower($role->name), '') : '',
            'is_student' => !empty($role->is_student) ? $role->is_student : 0,
            'default_color' => Settings::get_value('default_calendar_color_coding')
        ]);
    }

    /**
     * @project: 2018112901_lut
     * Description:
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @auth: ChienKV - khuongchien@gmail.com
     * @version: 1.0
     */
    public function doStoreSignup(Request $request)
    {
        try {
            $role = strtolower(Settings::get_value('default_signup_role'));
            $data = Role::findByName($role);
            $role = $role == 'teacher' ? 'teacher' : (isset($data->is_student) && $data->is_student ? 'student' : 'user');

            $request->merge([
                'name' => isset($request->fullname) ? $request->fullname : $request->firstname . ' ' . $request->lastname,
                'username' => $request->nickname
            ]);

            $userdata = [
                'email'     => $request->email,
                'name'      => $request->name,
                'username'  => $request->username,
                'password'  => $request->password,
                'receive_emails' => 1,
            ];
            $UserRequest = new UserRequest();
            $vald = Validator::make($request->all(), $UserRequest->rules());
            if ($vald->fails())
                return redirect()->back()->withErrors($vald->errors())->withInput();

            $func = 'doCreate'.ucfirst($role);
            $claz = 'App\Http\Requests\\'.ucfirst($role).'Request';
            $claz = class_exists($claz) ? new $claz() : null;
            if (!is_null($claz)) {
                $vald = Validator::make($request->all(), $claz->rules());
                if ($vald->fails())
                    return redirect()->back()->withErrors($vald->errors())->withInput();
            }

            $user = $this->create($userdata);

            if (isset($data->name)) $user->assignRole($data->name);
            if (method_exists($this, $func)) $this->$func($user->id, $request);

            NotificationHelper::sendNewUserNotification(User::find($user->id), $request->password);
            
            return redirect('/login')->with('success', __('messages.signupsuccess'));

            #$user->email_verified_at = Carbon::now()->toDateTimeString();
            #Auth::login($user);
            #return redirect('/schedule/monthly')->with('success', __('messages.signupsuccess'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * @project: 2018112901_lut
     * Description:
     * @param $uid
     * @param $request
     * @return int
     * @auth: ChienKV - khuongchien@gmail.com
     * @throws \Exception
     * @version: 1.0
     */
    private function doCreateStudent($uid, $request)
    {
        $role = Settings::get_value('default_signup_role');
        $role = Role::findByName($role);
        if (empty($role->is_student)) return 0;

        $student =  Students::create([
            'user_id' => $uid,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'lastname_kanji' => $request->lastname_kanji,
            'firstname_kanji' => $request->firstname_kanji,
            'lastname_furigana' => $request->lastname_furigana,
            'firstname_furigana' => $request->firstname_furigana,
            'home_phone' => $request->home_phone,
            'mobile_phone' => $request->mobile_phone,
            'email' => $request->email,
            'status' => 1,
            'join_date' => !empty($request->join_date) ? date('Y-m-d', strtotime($request->join_date)) : null,
            'address' => $request->address,
            'toiawase_referral' => $request->toiawase_referral ? $request->toiawase_referral : '',
            'toiawase_memo' => $request->toiawase_memo ? $request->toiawase_memo : '',
            'toiawase_getter' => $request->toiawase_getter ? $request->toiawase_getter : '',
            'toiawase_houhou' => $request->toiawase_houhou ? $request->toiawase_houhou : '',
            'toiawase_date' => $request->toiawase_date ? $request->toiawase_date : Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d'),
            'teacher_id' => 0,
            'birthday' => !empty($request->birthday) ? date('Y-m-d', strtotime($request->birthday)) : null,
            'comment' => $request->comment,
            'levels' => implode(",",$request->levels),
        ]);

        $student->updateAddressLatLong();
        return $student;
    }

    /**
     * @project: 2018112901_lut
     * Description:
     * @param $uid
     * @param $request
     * @return mixed
     * @auth: ChienKV - khuongchien@gmail.com
     * @version: 1.0
     */
    private function doCreateTeacher($uid, $request)
    {
        return Teachers::create([
            'name' => $request->fullname,
            'furigana' => $request->furigana,
            'nickname' => $request->nickname,
            'username' => $request->username,
            'birthday' => $request->birthday,
            'birthplace' => $request->birthplace,
            'profile' => $request->profile,
            'status'=> 0,
            'user_id' => $uid,
            'color_coding' => $request->color_coding
        ]);
    }
	/**
    private function doLogin(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    private function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }
	**/
}
