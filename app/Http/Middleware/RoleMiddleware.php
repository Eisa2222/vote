<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'حسابك معطل. تواصل مع الإدارة.');
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Redirect authenticated users to their proper dashboard instead of 403
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'تم توجيهك للوحة التحكم المناسبة لدورك.');
        }

        if ($user->hasAnyRole(['campaign-creator', 'campaign-reviewer', 'campaign-approver'])) {
            return redirect()->route('admin.workflow.index')
                ->with('error', 'تم توجيهك للوحة التحكم المناسبة لدورك.');
        }

        if ($user->hasRole('club-admin')) {
            return redirect()->route('club.dashboard')
                ->with('error', 'تم توجيهك للوحة التحكم المناسبة لدورك.');
        }

        abort(403, 'ليس لديك صلاحية للوصول لهذه الصفحة.');
    }
}
