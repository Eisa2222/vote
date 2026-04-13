<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CampaignAssignment;
use App\Models\Player;
use App\Models\VotingCampaign;
use App\Models\VotingLink;
use App\Services\SmsService;
use App\Services\VotingService;
use Illuminate\Http\Request;

class ClubCampaignController extends Controller
{
    public function __construct(
        private VotingService $votingService,
        private SmsService $smsService,
    ) {}

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

        $playersWithPhone = Player::where('club_id', $clubId)->active()->whereNotNull('phone')->where('phone', '!=', '')->count();
        $playersWithEmail = Player::where('club_id', $clubId)->active()->whereNotNull('email')->where('email', '!=', '')->count();

        return view('club.campaigns.show', compact('campaign', 'assignment', 'stats', 'links', 'playersWithPhone', 'playersWithEmail'));
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
        $sendMethod = $request->input('send_method', 'manual'); // manual, sms, email, both

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
        $smsCount = 0;
        $emailCount = 0;

        foreach ($links as $link) {
            $link->update(['status' => 'sent', 'sent_at' => now()]);
            $url = route('vote.show', $link->token);

            // Send SMS
            if (in_array($sendMethod, ['sms', 'both']) && $link->player?->phone) {
                $smsMessage = "دعوة تصويت: {$campaign->title}\nصوّت من خلال الرابط:\n{$url}";
                $this->smsService->send($link->player->phone, $smsMessage);
                $smsCount++;
            }

            // Send Email
            if (in_array($sendMethod, ['email', 'both']) && $link->player?->email) {
                try {
                    \Illuminate\Support\Facades\Notification::route('mail', $link->player->email)
                        ->notify(new \App\Notifications\VotingLinkNotification($link));
                    $emailCount++;
                } catch (\Exception $e) {
                    // Log silently
                }
            }

            $sentCount++;
        }

        $details = "تم إرسال {$sentCount} رابط";
        if ($smsCount > 0) $details .= " | SMS: {$smsCount}";
        if ($emailCount > 0) $details .= " | بريد: {$emailCount}";

        ActivityLog::log('send_links', $campaign, $details);

        return back()->with('success', $details);
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

    /**
     * Send shared voting link via SMS to all players with phone numbers
     */
    public function sendSharedSms(VotingCampaign $campaign)
    {
        $clubId = auth()->user()->club_id;

        $assignment = CampaignAssignment::where('campaign_id', $campaign->id)
            ->where('club_id', $clubId)
            ->firstOrFail();

        $players = Player::where('club_id', $clubId)
            ->active()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        $url = $assignment->shared_url;
        $message = "دعوة تصويت: {$campaign->title}\nللمشاركة افتح الرابط وأدخل رقم هويتك:\n{$url}";

        $sent = 0;
        foreach ($players as $player) {
            $result = $this->smsService->send($player->phone, $message);
            if ($result['success']) $sent++;
        }

        ActivityLog::log('send_shared_sms', $campaign, "تم إرسال الرابط الموحد عبر SMS إلى {$sent} لاعب");

        return back()->with('success', "تم إرسال الرابط الموحد عبر SMS إلى {$sent} لاعب من أصل {$players->count()}");
    }

    /**
     * Send shared voting link via Email to all players with email
     */
    public function sendSharedEmail(VotingCampaign $campaign)
    {
        $clubId = auth()->user()->club_id;

        $assignment = CampaignAssignment::where('campaign_id', $campaign->id)
            ->where('club_id', $clubId)
            ->firstOrFail();

        $players = Player::where('club_id', $clubId)
            ->active()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        $url = $assignment->shared_url;
        $sent = 0;

        foreach ($players as $player) {
            try {
                \Illuminate\Support\Facades\Mail::send([], [], function ($mail) use ($player, $campaign, $url) {
                    $mail->to($player->email)
                        ->subject("دعوة تصويت: {$campaign->title}")
                        ->html("
                            <div style='direction:rtl;font-family:Arial,sans-serif;padding:20px;'>
                                <h2>مرحباً {$player->name}</h2>
                                <p>تم إرسال دعوة تصويت لك في حملة: <strong>{$campaign->title}</strong></p>
                                <p><a href='{$url}' style='background:#667eea;color:#fff;padding:12px 30px;border-radius:8px;text-decoration:none;display:inline-block;margin:10px 0;'>ابدأ التصويت</a></p>
                                <p style='color:#888;font-size:13px;'>عند فتح الرابط، أدخل رقم هويتك أو جوالك للتحقق.</p>
                            </div>
                        ");
                });
                $sent++;
            } catch (\Exception $e) {
                // continue
            }
        }

        ActivityLog::log('send_shared_email', $campaign, "تم إرسال الرابط الموحد عبر البريد إلى {$sent} لاعب");

        return back()->with('success', "تم إرسال الرابط الموحد عبر البريد إلى {$sent} لاعب من أصل {$players->count()}");
    }
}
