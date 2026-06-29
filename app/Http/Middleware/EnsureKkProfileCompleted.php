<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\KkProfile;

class EnsureKkProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->role === 'user') {
            $profile = KkProfile::where('email', $user->email)->first();
            if (!$profile) {
                return redirect()->route('profile.my-requests')
                    ->with('error', 'You must complete your Katipunan ng Kabataan self-profiling registry first (100% complete) to access service requests and sports league registrations.');
            }
            if ($profile->status === 'pending') {
                return redirect()->route('profile.my-requests')
                    ->with('error', 'Your KK Profiling registry is currently pending review by our desk officers. All services will be unlocked once approved.');
            }
            if ($profile->status === 'declined') {
                return redirect()->route('profile.my-requests')
                    ->with('error', 'Your KK Profiling registry has been declined. Please re-submit your profiling details.');
            }
        }

        return $next($request);
    }
}
