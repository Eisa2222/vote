<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    /**
     * Workflow roles managed by this controller
     */
    private array $allowedRoles = ['campaign-creator', 'campaign-reviewer', 'campaign-approver'];

    private function resolveStaff(User $user): User
    {
        if (!$user->hasAnyRole($this->allowedRoles)) {
            abort(404);
        }
        return $user;
    }

    public function index()
    {
        $staff = User::role($this->allowedRoles)->with('roles')->latest()->paginate(15);
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        $roles = $this->allowedRoles;
        return view('admin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', Password::defaults()],
            'role' => 'required|in:campaign-creator,campaign-reviewer,campaign-approver',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => $request->has('is_active'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        ActivityLog::log('create', $user, 'تم إنشاء موظف جمعية: ' . $user->name . ' (' . $validated['role'] . ')');

        return redirect()->route('admin.staff.index')->with('success', 'تم إنشاء الموظف بنجاح');
    }

    public function edit(User $user)
    {
        $this->resolveStaff($user);
        $roles = $this->allowedRoles;
        $currentRole = $user->getRoleNames()->first();
        return view('admin.staff.edit', compact('user', 'roles', 'currentRole'));
    }

    public function update(Request $request, User $user)
    {
        $this->resolveStaff($user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', Password::defaults()],
            'role' => 'required|in:campaign-creator,campaign-reviewer,campaign-approver',
        ]);

        $old = $user->toArray();
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        $user->syncRoles([$validated['role']]);

        ActivityLog::log('update', $user, 'تم تحديث موظف جمعية: ' . $user->name, $old, $user->toArray());

        return redirect()->route('admin.staff.index')->with('success', 'تم تحديث الموظف بنجاح');
    }

    public function destroy(User $user)
    {
        $this->resolveStaff($user);
        ActivityLog::log('delete', $user, 'تم حذف موظف جمعية: ' . $user->name);
        $user->delete();
        return redirect()->route('admin.staff.index')->with('success', 'تم حذف الموظف بنجاح');
    }

    public function toggleStatus(User $user)
    {
        $this->resolveStaff($user);
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'تفعيل' : 'تعطيل';
        ActivityLog::log('toggle_status', $user, "تم {$status} الموظف: " . $user->name);
        return back()->with('success', "تم {$status} الموظف بنجاح");
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->resolveStaff($user);
        $request->validate(['password' => ['required', Password::defaults()]]);
        $user->update(['password' => Hash::make($request->password)]);
        ActivityLog::log('reset_password', $user, 'تم إعادة تعيين كلمة المرور للموظف: ' . $user->name);
        return back()->with('success', 'تم إعادة تعيين كلمة المرور بنجاح');
    }
}
