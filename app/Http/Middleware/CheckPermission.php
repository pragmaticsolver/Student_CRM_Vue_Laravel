<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Permission;

class CheckPermission
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
        if ( !Auth::user()->check_can_login() ) {
            \Auth::logout();
            return redirect('/');
        }
        $request_route_name = $request->route()->getName();

        // Check regular routes where ONE permission has to be checked for a single route.
        foreach(Permission::ROUTE_MATCH as $permission => $routes) {
            if(in_array($request_route_name, $routes)) {
                if(Auth::user()->hasPermissionTo($permission)) {
                    return $next($request);
                } else {
                    return abort('403');
                }
            }
        }

        // Check routes where "ANY ONE" permission should be assigned to access the route.
        if(isset(Permission::ANY_ONE_PERMISSION[$request_route_name]))
        {
            $permissions = Permission::ANY_ONE_PERMISSION[$request_route_name];
            foreach($permissions as $permission)
            {
                if(Auth::user()->hasPermissionTo($permission))
                {
                    return $next($request);
                }
            }
            return abort('403'); // If user do not have atleast one of the required permissions thorw error
        }

        // if not defined in permissons file, then pass
        return $next($request);
    }
}
