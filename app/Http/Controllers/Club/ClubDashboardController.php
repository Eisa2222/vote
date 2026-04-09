<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\CampaignAssignment;
use App\Models\Player;
use App\Models\VotingLink;
use App\Models\VotingResponse;

class ClubDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $clubId = $user->club_id;

        $stats = [
            'players_count' => Player::where('club_id', $clubId)->count(),
            'active_players' => Player::where('club_id', $clubId)->active()->count(),
            'campaigns_received' => CampaignAssignment::where('club_id', $clubId)->count(),
            'links_generated' => VotingLink::where('club_id', $clubId)->count(),
            'links_sent' => VotingLink::where('club_id', $clubId)->whereIn('status', ['sent', 'used'])->count(),
            'total_voted' => VotingResponse::where('club_id', $clubId)->count(),
            'participation_rate' => VotingLink::where('club_id', $clubId)->count() > 0
                ? round((VotingResponse::where('club_id', $clubId)->count() / VotingLink::where('club_id', $clubId)->count()) * 100, 1)
                : 0,
        ];

        $recentAssignments = CampaignAssignment::where('club_id', $clubId)
            ->with('campaign')
            ->latest()
            ->take(5)
            ->get();

        return view('club.dashboard', compact('stats', 'recentAssignments'));
    }
}
