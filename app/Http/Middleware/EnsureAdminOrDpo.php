<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrDpo
{
    /**
     * Handle an incoming request.
     */
    // Sa app/Http/Middleware/EnsureAdminOrDpo.php

    public function handle(Request $request, Closure $next): Response
    {
        // Dati: admin o dpo
        // Ngayon: Superadmin na lang
        if ($request->user() && $request->user()->role === 'superadmin') {
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }
}
