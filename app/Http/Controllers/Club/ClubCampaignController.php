<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CampaignAssignment;
use App\Models\VotingCampaign;
use App\Models\VotingLink;
use App\Services\VotingService;
use Illuminate\Http\Request;

class ClubCampaignController extends Controller
{
    public function __construct(private VotingService $votingService) {}

    public function index()
    {
        $clubId = auth()->user()->club_id;

        $assignments = CampaignAssignment::where('club_id', $clubId)
            ->with(['campaign' => function ($q) {
                $q->withCount(['questions', 'responses']);
            }])
            ->latest()
            ->paginate(15);

        return view('club.campaigns.index', compact('assignments'));
    }

    public function show(VotingCampaign $campaign)
    {
        $clubId = auth()->user()->club_id;

        // Verify assignment
        $assignment = CampaignAssignment::where('campaign_id', $campaign->id)
            ->where('club_id', $clubId)
            ->firstOrFail();

        if (!$assignment->opened_at) {
            $assignment->update(['opened_at' => now()]);
        }

        $campaign->load('questions.options');
        $stats = $this->votingService->getCampaignStats($campaign, $clubId);

        $links = VotingLink::where('campaign_id', $campaign->id)
            ->where('club_id', $clubId)
            ->with('player')
            ->paginate(50);

        return view('club.campaigns.show', compact('campaign', 'assignment', 'stats', 'links'));
    }

    public function generateLinks(VotingCampaign $campaign)
    {
        $clubId = auth()->user()->club_id;

        // Verify assignment exists
        CampaignAssignment::where('campaign_id', $campaign->id)
            ->where('club_id', $clubId)
            ->firstOrFail();

        $count = $this->votingService->generateLinksForClub($campaign, $clubId, auth()->id());
        ActivityLog::log('generate_links', $campaign, "تم توليد {$count} رابط تصويت");

        return back()->with('success', "تم توليد {$count} رابط تصويت للاعبين");
    }

    public function sendLinks(Request $request, VotingCampaign $campaign)
    {
        $clubId = auth()->user()->club_id;

        $linkIds = $request->input('link_ids', []);

        $query = VotingLink::where('campaign_id', $campaign->id)
            ->where('club_id', $clubId)
            ->where('status', 'pending');

        if (!empty($linkIds)) {
            $query->whereIn('id', $linkIds);
        }

        $updated = $query->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        ActivityLog::log('send_links', $campaign, "تم إرسال {$updated} رابط تصويت");

        return back()->with('success', "تم تحديث حالة {$updated} رابط إلى مُرسل");
    }

    public function results(VotingCampaign $campaign)
    {
        $clubId = auth()->user()->club_id;

        CampaignAssignment::where('campaign_id', $campaign->id)
            ->where('club_id', $clubId)
            ->firstOrFail();

        $campaign->load('questions.options');
        $stats = $this->votingService->getCampaignStats($campaign, $clubId);

        $questionResults = [];
        foreach ($campaign->questions as $question) {
            $questionResults[$question->id] = $this->votingService->getQuestionResults($question->id, $clubId);
        }

        return view('club.campaigns.results', compact('campaign', 'stats', 'questionResults'));
    }
}
