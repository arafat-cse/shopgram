<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $adminRoles = ['Super Admin', 'Admin', 'Manager', 'Sales Executive', 'Inventory Manager', 'Order Manager', 'Customer Support'];

        if (!$user->hasAnyRole($adminRoles)) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
