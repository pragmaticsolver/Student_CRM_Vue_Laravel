<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use App\User;
use App\Students;
use App\Teachers;
use App\Http\Requests\UserRequest;
use App\AssessmentUsers;
use App\Helpers\NotificationHelper;
use App\LineAccountConnectNonce;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function index()
    {
        $users  =   User::all();
        return view('user.list',compact('users'));
    }

    public function create()
    {
        return view('user.create', [
            'roles' => \Spatie\Permission\Models\Role::all(),
            'students' => Students::orderBy('firstname','ASC')->orderBy('lastname','ASC')->get(),
        ]);
    }

    public function store(UserRequest $request)
    {
        try {
            $input = $request->only('email', 'name', 'username', 'password');
            $input['password'] = bcrypt($input['password']);
            $input['lang'] = User::getDefaultLanuage($request->role);
            $user = User::create($input);

            $user->assignRole($request->role);

            $role = Role::where('name', $request->role)->first();

            if($role->is_student) Students::createByUser($user);
            if($request->role == 'Teacher') Teachers::createByUser($user);

            $this->syncChildren($user, $request);

            NotificationHelper::sendNewUserNotification(User::find($user->id), $request->password);

            return redirect('/users')->with('success', 'User has been added!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $user  = User::find($id);
        $urole = $user->roles()->pluck('id')->toArray();
        return view('user.details',compact('user','urole'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('user.edit', [
                'user' => $user,
                'children_ids' => $user->children->pluck('id')->toArray(),
                'students' => Students::orderBy('firstname','ASC')->orderBy('lastname','ASC')->get(),
            ]);
    }

    public function update(UserRequest $request, $id)
    {
        try {
            $user = User::find($id);
            // email is optional here, may be passed, may not be passed in case of parent emai being used.
            if ('on' == $request->change_password) {
                $input = $request->only(['name', 'username', 'email', 'password']);
                $input['password'] = bcrypt($input['password']);
            } else {
                $input = $request->only(['name', 'username', 'email']);
            }

            $user->fill($input)->save();

            $this->syncChildren($user, $request);

            return redirect('/users')->with('success', 'User has been updated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function syncChildren($user, $request)
    {
        if($user->hasRole('parent'))
        {
            $selected_childrens = (array)$request->children;
            $user->children()->whereNotIn('id',$selected_childrens)->update([
                'parent_user_id' => NULL
            ]);
            Students::where('parent_user_id',NULL)->whereIn('id',$selected_childrens)->update([
                'parent_user_id' => $user->id
            ]);
        }
    }

    public function destroy($id)
    {
        //Find a user with a given id and delete
        $user = User::find($id);
        $user->delete();

        return redirect('/users')->with('success', 'User has been deleted successfully!');
    }

    public function assessment_list()
    {
        return view('user.assessment.list', ['assessment_users' => \Auth::user()->assessment_users]);
    }

    public function take_assessment($id, Request $request)
    {
        $assessment_user = AssessmentUsers::find($id);
        $assessment = $assessment_user->assessment;
        $schedule = $assessment_user->schedule;
        $questions = $assessment->assessment_questions;
        $user = $assessment_user->user;
        $inputs = [];
        $errors = [];
        $session = session('assessment_form_'.$assessment_user->id);
        if($session)
        {
            $inputs = $session['inputs'];
            $errors =  $session['errors'];
        }

        $show_warning_on_submit = \Auth::user()->can('edit-assessment-response') ? false : true;

        return view('user.assessment.take', [
            'assessment_user' => $assessment_user,
            'assessment' => $assessment,
            'schedule' => $schedule,
            'questions' => $questions,
            'user' => $user,
            'return_url' => $request->return_url,
            'inputs' => $inputs,
            'errors' => $errors,
            'show_warning_on_submit' => $show_warning_on_submit
        ]);
    }

    public function student_switch_start( $new_user, Request $request)
    {
        $new_user = User::find( $new_user );
        $user_role = $new_user->get_role();
        if(!$user_role)
        {
            return Redirect::back()->with(['error' => 'Cant Impersonate, user dont have any roles.']);
        }
        if($user_role->is_student != 1)
        {
            return Redirect::back()->with(['error' => 'Cant Impersonate, user doent have a valid student role.']);
        }

        $current_user = Auth::user();
        // parent user can impersonate only their children, not all students.
        if($current_user->hasRole('parent'))
        {
            $exists = $current_user->children()->where('students.user_id', $new_user->id)->exists();
            if(!$exists)
            {
                return Redirect::back()->with(['error' => 'Cant Impersonate, you dont have permission to impersonate that student.']);
            }
        }

        Session::put( 'orig_user', Auth::id() );
        $referrer = $request->headers->get('referer');
        Session::put( 'orig_route', $referrer);

        Auth::login( $new_user );
        return redirect($user_role->login_redirect_path);
    }

    public function student_switch_stop()
    {
        $id = Session::pull('orig_user');
        if(!$id)
        {
            return redirect('/');
        }
        $orig_route = Session::pull( 'orig_route' );
        $orig_user = User::find( $id );
        Auth::login( $orig_user );
        return Redirect::to($orig_route);
    }

    public function linkLineForceLogin($link_token)
    {
        \Auth::logout();
        Session::put('url.intended', route('link.line.account', $link_token));  
        return redirect(route('login', ['linking_line' => 1]));
    }
    
    public function linkLineAccount($link_token)
    {
        $user_id = Auth::user()->id;
        $nonce = base64_encode(sha1(time().$user_id.rand(111,999))); // Random string

        $lineAccountConnectNonce = new LineAccountConnectNonce();
        $lineAccountConnectNonce->user_id = $user_id;
        $lineAccountConnectNonce->nonce = $nonce;
        $lineAccountConnectNonce->save();

        $line_url = "https://access.line.me/dialog/bot/accountLink?linkToken=$link_token&nonce={$nonce}";
        return redirect($line_url);
    }
}
