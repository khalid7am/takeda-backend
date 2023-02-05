<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Role;
use App\Types\RoleType;
use App\Helpers\AppHelpers;
use Illuminate\Http\Request;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $roleCode)
    {
        $roleMatrix = AppHelpers::getRoleMatrix();

        if (auth()->check() && in_array(auth()->user()->role->code, $roleMatrix[$roleCode])) {
            return $next($request);
        }

        abort(403);
    }
}
