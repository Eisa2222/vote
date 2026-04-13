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

        $query = VotingLink::where('campaign_id', $campaign->id)
            ->where('club_id', $clubId)
            ->where('status', 'pending')
            ->with('player');

        $linkIds = $request->input('link_ids', []);
        if (!empty($linkIds)) {
            $query->whereIn('id', $linkIds);
        }

        $links = $query->get();
        $sentCount = 0;

        foreach ($links as $link) {
            $link->update(['status' => 'sent', 'sent_at' => now()]);

            // Send email if player has email
            if ($link->player && $link->player->email) {
                try {
                    \Illuminate\Support\Facades\Notification::route('mail', $link->player->email)
                        ->notify(new \App\Notifications\VotingLinkNotification($link));
                } catch (\Exception $e) {
                    // Log but don't block
                }
            }

            $sentCount++;
        }

        ActivityLog::log('send_links', $campaign, "تم إرسال {$sentCount} رابط تصويت");

        return back()->with('success', "تم إرسال {$sentCount} رابط تصويت للاعبين");
    }

    public function results(VotingCampaign $campaign)
    {
        $clubId = auth()->user()->club_id;

        CampaignAssignment::where('campaign_id', $campaign->id)
            ->where('club_id', $clubId)
            ->firstOrFail();

        // Private voting: results visible to super admin only
        if ($campaign->voting_type === 'private') {
            return back()->with('error', 'هذا تصويت خاص - النتائج مرئية للجمعية فقط.');
        }

        // Enforce results_visible_after for club admins
        if ($campaign->results_visible_after && $campaign->results_visible_after->isFuture()) {
            return back()->with('error', 'النتائج غير متاحة حالياً. ستكون متاحة بعد: ' . $campaign->results_visible_after->format('Y-m-d H:i'));
        }

        $campaign->load('questions.options');
        $stats = $this->votingService->getCampaignStats($campaign, $clubId);

        $questionResults = [];
        foreach ($campaign->questions as $question) {
            $questionResults[$question->id] = $this->votingService->getQuestionResults($question->id, $clubId);
        }

        return view('club.campaigns.results', compact('campaign', 'stats', 'questionResults'));
    }
}
