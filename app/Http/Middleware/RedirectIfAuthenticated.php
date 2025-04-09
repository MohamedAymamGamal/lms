<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // Prevent access to any login page if already authenticated
                if ($request->is('login') || $request->is('admin/login') || $request->is('instructor/login')) {
                    return match ($user->role) {
                        'user' => redirect('/dashboard'),
                        'instructor' => redirect('/instructor/dashboard'),
                        'admin' => redirect('/admin/dashboard'),
                        default => redirect('/login')->withErrors(['message' => 'Unauthorized role.']),
                    };
                }
            }
        }

        return $next($request);
    }
}
