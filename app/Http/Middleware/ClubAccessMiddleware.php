<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClubAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Club admin can only access their own club data
        if ($user->hasRole('club-admin')) {
            $clubId = $request->route('club') ?? $request->input('club_id');

            if ($clubId && (int) $clubId !== (int) $user->club_id) {
                abort(403, 'لا يمكنك الوصول لبيانات نادي آخر.');
            }
        }

        return $next($request);
    }
}
