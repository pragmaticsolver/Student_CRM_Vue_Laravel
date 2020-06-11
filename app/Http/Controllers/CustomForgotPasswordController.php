<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PasswordResetToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomForgotPasswordController extends Controller
{
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetPasswordLink(Request $request)
    {
        $message = __('messages.password-reset-link-has-been-sent-to-your-email-address');
        $user = User::where('username', $request->username)->first();
        if($user)
        {
            $passwordResetToken = new PasswordResetToken();
            $passwordResetToken->user_id = $user->id;
            $passwordResetToken->token = Str::random(60);
            $passwordResetToken->save();

            $reset_password_link = route('reset-password', $passwordResetToken->token);
            NotificationHelper::sendResetPasswordLinkNotification($user, $reset_password_link);

            if($user->willUseParentEmail())
            {
                $message = __('messages.password-reset-link-has-been-sent-to-parent-email-address');
            }
        }
        return redirect(route('forgot-password'))->with('success', $message);
    }

    public function resetPassword($token, Request $request)
    {
        $passwordRestToken = PasswordResetToken::where('token', $token)->first();
        return view('auth.reset-password', [ 'passwordRestToken' => $passwordRestToken ]);
    }

    public function resetPasswordSubmit(Request $request)
    {
        $rules = [];
        $rules['username'] = 'required';
        $rules['password'] = 'required|min:6';
        $rules['confirm_password'] = 'required|same:password';

        $messages = [
            'password.min' => __('messages.new-password-must-be-at-least-min-characters'),
            'confirm_password.same' => __('messages.new-password-and-confirm-password-must-match')
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setCustomMessages($messages);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('username', $request->username)->first();
        if(!$user)
        {
            return redirect()->back()->with('error',__('messages.invalid-username'))->withInput();
        }

        $passwordRestToken = PasswordResetToken::where('user_id', $user->id)->where('token', $request->token)->first();
        if(!$passwordRestToken)
        {
            return redirect()->back()->with('error',__('messages.invalid-password-reset-link'))->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $passwordRestToken->delete();

        return redirect(route('login'))->with('success', __('messages.password-updated-successfully'));
    }
}
