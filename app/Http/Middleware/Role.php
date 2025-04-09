<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login')->withErrors(['message' => 'Please log in first.']);
        }

        $user = Auth::user();

        // Prevent users from accessing login pages once authenticated
        if ($this->isLoginPage($request)) {
            return redirect($this->getDashboardRoute($user->role));
        }

        // Check if the user has the correct role
        if ($user->role !== $role) {
            return redirect($this->getDashboardRoute($user->role))
                ->withErrors(['message' => 'Unauthorized access.']);
        }

        return $next($request);
    }

    /**
     * Get the correct dashboard route based on user role.
     */
    private function getDashboardRoute(string $role): string
    {
        return match ($role) {
            'admin' => '/admin/dashboard',
            'instructor' => '/instructor/dashboard',
            'user' => '/dashboard',
            default => '/login',
        };
    }
    /**
     * Check if the request is for a login page.
     */
    private function isLoginPage(Request $request): bool
    {
        return $request->is('login') || $request->is('admin/login') || $request->is('instructor/login');
    }
}
