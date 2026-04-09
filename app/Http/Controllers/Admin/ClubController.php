<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Club;
use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClubController extends Controller
{
    public function index(Request $request)
    {
        $query = Club::with('league')->withCount(['players', 'admins']);

        if ($request->filled('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        $clubs = $query->latest()->paginate(15);
        $leagues = League::active()->get();

        return view('admin.clubs.index', compact('clubs', 'leagues'));
    }

    public function create()
    {
        $leagues = League::active()->get();
        $selectedLeague = request('league_id');
        return view('admin.clubs.create', compact('leagues', 'selectedLeague'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'league_id' => 'required|exists:leagues,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('clubs/logos', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $club = Club::create($validated);
        ActivityLog::log('create', $club, 'تم إنشاء النادي: ' . $club->name_ar);

        return redirect()->route('admin.clubs.index')->with('success', 'تم إنشاء النادي بنجاح');
    }

    public function edit(Club $club)
    {
        $leagues = League::active()->get();
        return view('admin.clubs.edit', compact('club', 'leagues'));
    }

    public function update(Request $request, Club $club)
    {
        $validated = $request->validate([
            'league_id' => 'required|exists:leagues,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $old = $club->toArray();

        if ($request->hasFile('logo')) {
            if ($club->logo) {
                Storage::disk('public')->delete($club->logo);
            }
            $validated['logo'] = $request->file('logo')->store('clubs/logos', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $club->update($validated);
        ActivityLog::log('update', $club, 'تم تحديث النادي: ' . $club->name_ar, $old, $club->toArray());

        return redirect()->route('admin.clubs.index')->with('success', 'تم تحديث النادي بنجاح');
    }

    public function destroy(Club $club)
    {
        ActivityLog::log('delete', $club, 'تم حذف النادي: ' . $club->name_ar);
        $club->delete();

        return redirect()->route('admin.clubs.index')->with('success', 'تم حذف النادي بنجاح');
    }

    public function toggleStatus(Club $club)
    {
        $club->update(['is_active' => !$club->is_active]);
        $status = $club->is_active ? 'تفعيل' : 'تعطيل';
        ActivityLog::log('toggle_status', $club, "تم {$status} النادي: " . $club->name_ar);

        return back()->with('success', "تم {$status} النادي بنجاح");
    }
}
