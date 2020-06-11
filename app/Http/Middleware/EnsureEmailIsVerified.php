<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Session;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Do not check this rule if impersonating someone by admin
        if(!Session::get('orig_user'))
        {
            if (! $request->user() ||
                ($request->user() instanceof MustVerifyEmail &&
                !$request->user()->hasVerifiedEmail())) {
                return $request->expectsJson()
                        ? abort(403, 'Your email address is not verified.')
                        : Redirect::route('verification.notice');
            }
        }

        return $next($request);
    }
}
