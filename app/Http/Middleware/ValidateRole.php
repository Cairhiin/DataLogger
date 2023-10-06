<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class ValidateRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $userId = Auth::id();
        $user = User::where('id', $userId)->first();

        if ($user->role->name != "Member" && $user->role->name != "Admin" && $user->role->name != "Super Admin") {
            return redirect(RouteServiceProvider::HOME);
        }

        return $response;
    }
}
