<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Imports\PlayersImport;
use App\Models\ActivityLog;
use App\Models\Import;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ClubPlayerController extends Controller
{
    public function index(Request $request)
    {
        $clubId = auth()->user()->club_id;

        $query = Player::where('club_id', $clubId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('national_id', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $players = $query->latest()->paginate(20);
        return view('club.players.index', compact('players'));
    }

    public function create()
    {
        return view('club.players.create');
    }

    public function store(Request $request)
    {
        $clubId = auth()->user()->club_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'national_id' => "nullable|string|unique:players,national_id,NULL,id,club_id,{$clubId}",
            'player_number' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'nationality' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,suspended,injured,retired,inactive',
        ]);

        $validated['club_id'] = $clubId;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('players/photos', 'public');
        }

        $player = Player::create($validated);
        ActivityLog::log('create', $player, 'تم إضافة لاعب: ' . $player->name);

        return redirect()->route('club.players.index')->with('success', 'تم إضافة اللاعب بنجاح');
    }

    public function edit(Player $player)
    {
        $this->authorizeClub($player);
        return view('club.players.edit', compact('player'));
    }

    public function update(Request $request, Player $player)
    {
        $this->authorizeClub($player);
        $clubId = auth()->user()->club_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'national_id' => "nullable|string|unique:players,national_id,{$player->id},id,club_id,{$clubId}",
            'player_number' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'nationality' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,suspended,injured,retired,inactive',
        ]);

        if ($request->hasFile('photo')) {
            if ($player->photo) {
                Storage::disk('public')->delete($player->photo);
            }
            $validated['photo'] = $request->file('photo')->store('players/photos', 'public');
        }

        $player->update($validated);
        ActivityLog::log('update', $player, 'تم تحديث لاعب: ' . $player->name);

        return redirect()->route('club.players.index')->with('success', 'تم تحديث اللاعب بنجاح');
    }

    public function destroy(Player $player)
    {
        $this->authorizeClub($player);
        ActivityLog::log('delete', $player, 'تم حذف لاعب: ' . $player->name);
        $player->delete();

        return redirect()->route('club.players.index')->with('success', 'تم حذف اللاعب بنجاح');
    }

    public function importForm()
    {
        return view('club.players.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        $clubId = auth()->user()->club_id;
        $file = $request->file('file');
        $path = $file->store('imports', 'public');

        $import = Import::create([
            'user_id' => auth()->id(),
            'club_id' => $clubId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'processing',
        ]);

        try {
            $importer = new PlayersImport($clubId, $import);
            Excel::import($importer, storage_path('app/public/' . $path));

            $import->update([
                'status' => 'completed',
                'total_rows' => $importer->getRowCount(),
                'success_rows' => $importer->getSuccessCount(),
                'failed_rows' => $importer->getFailedCount(),
                'errors' => $importer->getErrors(),
            ]);

            return redirect()->route('club.players.index')
                ->with('success', "تم الاستيراد: {$importer->getSuccessCount()} نجاح، {$importer->getFailedCount()} فشل");
        } catch (\Exception $e) {
            $import->update(['status' => 'failed', 'errors' => ['general' => $e->getMessage()]]);
            return back()->with('error', 'فشل الاستيراد: ' . $e->getMessage());
        }
    }

    private function authorizeClub(Player $player): void
    {
        if ($player->club_id !== auth()->user()->club_id) {
            abort(403, 'لا يمكنك الوصول لهذا اللاعب.');
        }
    }
}
