<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Club;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $query = Player::with('club');

        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }
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
        $clubs = Club::active()->get();

        return view('admin.players.index', compact('players', 'clubs'));
    }

    public function show(Player $player)
    {
        $player->load('club', 'votingLinks.campaign', 'votingResponses');
        return view('admin.players.show', compact('player'));
    }
}
