<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeagueController extends Controller
{
    public function index()
    {
        $leagues = League::withCount('clubs')->latest()->paginate(15);
        return view('admin.leagues.index', compact('leagues'));
    }

    public function create()
    {
        return view('admin.leagues.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'season' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('leagues/logos', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $league = League::create($validated);
        ActivityLog::log('create', $league, 'تم إنشاء الدوري: ' . $league->name_ar);

        return redirect()->route('admin.leagues.index')->with('success', 'تم إنشاء الدوري بنجاح');
    }

    public function edit(League $league)
    {
        return view('admin.leagues.edit', compact('league'));
    }

    public function update(Request $request, League $league)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'season' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $old = $league->toArray();

        if ($request->hasFile('logo')) {
            if ($league->logo) {
                Storage::disk('public')->delete($league->logo);
            }
            $validated['logo'] = $request->file('logo')->store('leagues/logos', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $league->update($validated);
        ActivityLog::log('update', $league, 'تم تحديث الدوري: ' . $league->name_ar, $old, $league->toArray());

        return redirect()->route('admin.leagues.index')->with('success', 'تم تحديث الدوري بنجاح');
    }

    public function destroy(League $league)
    {
        if ($league->clubs()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الدوري لأنه يحتوي على أندية. انقل الأندية أولاً.');
        }

        ActivityLog::log('delete', $league, 'تم حذف الدوري: ' . $league->name_ar);
        $league->delete();

        return redirect()->route('admin.leagues.index')->with('success', 'تم حذف الدوري بنجاح');
    }

    public function toggleStatus(League $league)
    {
        $league->update(['is_active' => !$league->is_active]);
        $status = $league->is_active ? 'تفعيل' : 'تعطيل';
        ActivityLog::log('toggle_status', $league, "تم {$status} الدوري: " . $league->name_ar);

        return back()->with('success', "تم {$status} الدوري بنجاح");
    }

    public function show(League $league)
    {
        $league->load(['clubs' => fn($q) => $q->withCount('players')]);
        return view('admin.leagues.show', compact('league'));
    }
}
