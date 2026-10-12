<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// Admin OR Company users can open the panel
class PanelMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->user();
        abort_unless($u && ($u->isAdmin() || $u->isCompany()), 403, 'Access denied.');
        return $next($request);
    }
}
