<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::role('club-admin')->with('club')->latest()->paginate(15);
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $clubs = Club::active()->get();
        return view('admin.admins.create', compact('clubs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', Password::defaults()],
            'club_id' => 'required|exists:clubs,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');
        $validated['email_verified_at'] = now();

        $user = User::create($validated);
        $user->assignRole('club-admin');

        ActivityLog::log('create', $user, 'تم إنشاء إداري: ' . $user->name);

        return redirect()->route('admin.admins.index')->with('success', 'تم إنشاء الإداري بنجاح');
    }

    public function edit(User $admin)
    {
        $clubs = Club::active()->get();
        return view('admin.admins.edit', compact('admin', 'clubs'));
    }

    public function update(Request $request, User $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', Password::defaults()],
            'club_id' => 'required|exists:clubs,id',
            'is_active' => 'boolean',
        ]);

        $old = $admin->toArray();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $admin->update($validated);
        ActivityLog::log('update', $admin, 'تم تحديث الإداري: ' . $admin->name, $old, $admin->toArray());

        return redirect()->route('admin.admins.index')->with('success', 'تم تحديث الإداري بنجاح');
    }

    public function destroy(User $admin)
    {
        ActivityLog::log('delete', $admin, 'تم حذف الإداري: ' . $admin->name);
        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', 'تم حذف الإداري بنجاح');
    }

    public function toggleStatus(User $admin)
    {
        $admin->update(['is_active' => !$admin->is_active]);
        $status = $admin->is_active ? 'تفعيل' : 'تعطيل';
        ActivityLog::log('toggle_status', $admin, "تم {$status} الإداري: " . $admin->name);

        return back()->with('success', "تم {$status} الإداري بنجاح");
    }

    public function resetPassword(Request $request, User $admin)
    {
        $request->validate(['password' => ['required', Password::defaults()]]);
        $admin->update(['password' => Hash::make($request->password)]);
        ActivityLog::log('reset_password', $admin, 'تم إعادة تعيين كلمة المرور للإداري: ' . $admin->name);

        return back()->with('success', 'تم إعادة تعيين كلمة المرور بنجاح');
    }
}
