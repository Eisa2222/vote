<?php

namespace App\Services;

use App\Models\CampaignAssignment;
use App\Models\Player;
use App\Models\VotingCampaign;
use App\Models\VotingLink;
use App\Models\VotingResponse;
use App\Models\VotingResponseAnswer;
use App\Models\VotingSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VotingService
{
    public function generateLinksForClub(VotingCampaign $campaign, int $clubId, int $generatedBy): int
    {
        $players = Player::where('club_id', $clubId)->active()->get();
        $count = 0;

        foreach ($players as $player) {
            $exists = VotingLink::where('campaign_id', $campaign->id)
                ->where('player_id', $player->id)
                ->exists();

            if (!$exists) {
                VotingLink::create([
                    'campaign_id' => $campaign->id,
                    'player_id' => $player->id,
                    'club_id' => $clubId,
                    'generated_by' => $generatedBy,
                    'token' => VotingLink::generateToken(),
                    'status' => 'pending',
                    'expires_at' => $campaign->ends_at,
                ]);
                $count++;
            }
        }

        return $count;
    }

    public function submitVote(VotingLink $link, array $answers, string $ip, string $userAgent): VotingResponse
    {
        return DB::transaction(function () use ($link, $answers, $ip, $userAgent) {
            // Create session
            $session = VotingSession::create([
                'voting_link_id' => $link->id,
                'campaign_id' => $link->campaign_id,
                'player_id' => $link->player_id,
                'club_id' => $link->club_id,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'started_at' => now(),
                'submitted_at' => now(),
                'is_completed' => true,
            ]);

            // Create response
            $response = VotingResponse::create([
                'session_id' => $session->id,
                'campaign_id' => $link->campaign_id,
                'player_id' => $link->player_id,
                'club_id' => $link->club_id,
            ]);

            // Save answers
            foreach ($answers as $questionId => $answer) {
                if (is_array($answer)) {
                    // Checkbox - multiple options
                    foreach ($answer as $optionId) {
                        VotingResponseAnswer::create([
                            'response_id' => $response->id,
                            'question_id' => $questionId,
                            'option_id' => $optionId,
                        ]);
                    }
                } else {
                    VotingResponseAnswer::create([
                        'response_id' => $response->id,
                        'question_id' => $questionId,
                        'option_id' => is_numeric($answer) ? $answer : null,
                        'text_answer' => is_numeric($answer) ? null : $answer,
                    ]);
                }
            }

            // Mark link as used
            $link->markAsUsed();

            return $response;
        });
    }

    public function getCampaignStats(VotingCampaign $campaign, ?int $clubId = null): array
    {
        $linksQuery = VotingLink::where('campaign_id', $campaign->id);
        $responsesQuery = VotingResponse::where('campaign_id', $campaign->id);

        if ($clubId) {
            $linksQuery->where('club_id', $clubId);
            $responsesQuery->where('club_id', $clubId);
        }

        $totalLinks = $linksQuery->count();
        $totalVoted = $responsesQuery->count();
        $totalSent = (clone $linksQuery)->whereIn('status', ['sent', 'used'])->count();

        return [
            'total_links' => $totalLinks,
            'total_sent' => $totalSent,
            'total_voted' => $totalVoted,
            'total_not_voted' => $totalLinks - $totalVoted,
            'participation_rate' => $totalLinks > 0 ? round(($totalVoted / $totalLinks) * 100, 1) : 0,
        ];
    }

    public function getQuestionResults(int $questionId, ?int $clubId = null): array
    {
        $query = VotingResponseAnswer::where('question_id', $questionId)
            ->with('option');

        if ($clubId) {
            $query->whereHas('response', fn($q) => $q->where('club_id', $clubId));
        }

        $answers = $query->get();
        $totalResponses = $answers->groupBy('response_id')->count();

        $results = $answers->groupBy('option_id')->map(function ($group) use ($totalResponses) {
            $option = $group->first()->option;
            return [
                'option_id' => $option?->id,
                'label' => $option?->label ?? 'نص حر',
                'count' => $group->count(),
                'percentage' => $totalResponses > 0 ? round(($group->count() / $totalResponses) * 100, 1) : 0,
            ];
        })->values()->toArray();

        return [
            'total_responses' => $totalResponses,
            'results' => $results,
        ];
    }
}
