<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\League;
use App\Models\Player;
use App\Models\User;
use App\Models\VotingCampaign;
use App\Models\VotingLink;
use App\Models\VotingResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'leagues_count' => League::count(),
            'clubs_count' => Club::count(),
            'admins_count' => User::role('club-admin')->count(),
            'players_count' => Player::count(),
            'campaigns_count' => VotingCampaign::count(),
            'active_campaigns' => VotingCampaign::active()->count(),
            'total_votes' => VotingResponse::count(),
            'total_links' => VotingLink::count(),
            'participation_rate' => VotingLink::count() > 0
                ? round((VotingResponse::count() / VotingLink::count()) * 100, 1)
                : 0,
        ];

        $recentCampaigns = VotingCampaign::latest()->take(5)->get();
        $clubStats = Club::withCount(['players', 'votingLinks'])->get();

        return view('admin.dashboard', compact('stats', 'recentCampaigns', 'clubStats'));
    }
}
