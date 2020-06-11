<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ActivityEnum;
use App\Helpers\ActivityLogHelper;
use App\Helpers\LineClient;
use App\Http\Controllers\Controller;
use App\Settings;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
	protected function authenticated(Request $request, $user)
    {
        ActivityLogHelper::create(ActivityEnum::USER_LOGGEDIN, $user->id, NULL);

        if(count(array_filter(explode('/',session('url.intended')))) > count(array_filter(explode('/',env('APP_URL')))))
        {
            return redirect()->intended();
        }

        $user_role = $user->get_role();
        
        if($user_role) {
            if ($user_role->can_login) {
                return redirect($user_role->login_redirect_path);
            } else {
                $this->guard()->logout();
            }
        } 
        return redirect('/');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $loggedin_via_terminal = session('loggedin_via_terminal');
        $request->session()->invalidate();

        if($loggedin_via_terminal == 1)
        {
            return redirect(route('terminal.index'));
        }

        return $this->loggedOut($request) ?: redirect('/');
    }


    public function loginWithLine(Request $request)
    {
        $use_login_with_line = Settings::get_value('use_login_with_line');
        if (!$use_login_with_line) {
            return redirect(route('login'));
        }

        $channel_id = Settings::get_value('line_login_channel_id');
        $redirect_uri = route('login.line.callback');
        $state = Str::random(10);
        $scope = "profile";

        session()->put('line-login-state', $state);

        $url = "https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=$channel_id&redirect_uri=$redirect_uri&state=$state&scope=$scope";
        return redirect($url);
    }

    public function loginWithLineCallback(Request $request)
    {
        $error_message = __('messages.failed-to-login-with-line');
        $error_message_1 = __('messages.failed-to-login-with-line-please-link-your-line-account-first');

        if($request->state != session()->get('line-login-state'))
        {
            return redirect()->route('login')->with('line_login_error', $error_message);
        }

        if($request->error)
        {
            return redirect()->route('login')->with('line_login_error', $error_message);
        }

        $lineClient = new LineClient();
        $res = $lineClient->getAccessToken($request->code);
        if($res['status'] == 0) 
        {
            return redirect()->route('login')->with('line_login_error', $error_message);
        }

        $res = $lineClient->getUserProfile($res['access_token']);
        if($res['status'] == 0)
        {
            return redirect()->route('login')->with('line_login_error', $error_message);
        }
        
        $line_user_id = $res['userId'];
        $user = User::where('line_user_id', $line_user_id)->first();
        if(!$user)
        {
            return redirect()->route('login')->with('line_login_error', $error_message_1);
        }

        \Auth::login($user);
        return $this->sendLoginResponse($request);
    }
}
