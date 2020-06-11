<?php

namespace App\Http\Middleware;

use Closure;

class HomepageRedirect
{
    public function handle($request, Closure $next)
    {
        $current_user = \Auth::user();

        if($current_user->hasRole('student')) {
            return redirect('schedule/calendar');
        } elseif ($current_user->hasRole('Teacher')) {
            return redirect('teacher/schedule/list');
        } elseif ($current_user->hasRole('librarian')) {
            return redirect('book');
        } else {
            return $next($request);
        }
    }
}
