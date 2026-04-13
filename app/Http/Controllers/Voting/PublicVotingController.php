<?php

namespace App\Http\Controllers\Voting;

use App\Http\Controllers\Controller;
use App\Models\CampaignAssignment;
use App\Models\Player;
use App\Models\VotingLink;
use App\Models\VotingResponse;
use App\Services\VotingService;
use Illuminate\Http\Request;

class PublicVotingController extends Controller
{
    public function __construct(private VotingService $votingService) {}

    // ──────────────────────────────────────────────
    //  Shared Link Flow (رابط واحد لجميع اللاعبين)
    // ──────────────────────────────────────────────

    /**
     * Step 1: Show identity verification form
     */
    public function shared(string $token)
    {
        $assignment = CampaignAssignment::where('shared_token', $token)
            ->with(['campaign', 'club'])
            ->first();

        if (!$assignment) {
            return view('voting.invalid');
        }

        $campaign = $assignment->campaign;

        if (!$campaign->isActive()) {
            return view('voting.expired', compact('campaign'));
        }

        return view('voting.verify', [
            'token' => $token,
            'campaign' => $campaign,
            'club' => $assignment->club,
            'mode' => 'shared',
        ]);
    }

    /**
     * Step 2: Verify identity → find player → show form or already-voted
     */
    public function sharedVerify(Request $request, string $token)
    {
        $request->validate([
            'national_id' => 'required|string',
        ], [
            'national_id.required' => 'يرجى إدخال رقم الهوية أو رقم الجوال.',
        ]);

        $assignment = CampaignAssignment::where('shared_token', $token)
            ->with(['campaign.questions.options', 'club'])
            ->firstOrFail();

        $campaign = $assignment->campaign;

        if (!$campaign->isActive()) {
            return redirect()->route('vote.shared', $token);
        }

        // Find player in this club by national_id OR phone
        $input = trim($request->input('national_id'));
        $player = Player::where('club_id', $assignment->club_id)
            ->where(fn($q) => $q->where('national_id', $input)->orWhere('phone', $input))
            ->first();

        if (!$player) {
            return back()->withErrors(['national_id' => 'لم يتم العثور على بياناتك. تأكد من رقم الهوية أو الجوال المسجل لدى النادي.']);
        }

        // Check if already voted
        $alreadyVoted = VotingResponse::where('campaign_id', $campaign->id)
            ->where('player_id', $player->id)
            ->exists();

        if ($alreadyVoted) {
            return view('voting.already-voted', ['player' => $player]);
        }

        // Get or create the player's voting link
        $link = VotingLink::firstOrCreate(
            ['campaign_id' => $campaign->id, 'player_id' => $player->id],
            [
                'club_id' => $assignment->club_id,
                'generated_by' => $assignment->admin_id,
                'token' => VotingLink::generateToken(),
                'status' => 'sent',
                'sent_at' => now(),
                'expires_at' => $campaign->ends_at,
            ]
        );

        if ($link->status === 'used') {
            return view('voting.already-voted', ['player' => $player]);
        }

        $questions = $campaign->questions()->active()->with('options')->orderBy('sort_order')->get();

        return view('voting.form', [
            'link' => $link,
            'campaign' => $campaign,
            'questions' => $questions,
            'token' => $link->token,
        ]);
    }

    // ──────────────────────────────────────────────
    //  Individual Link Flow (رابط فردي لكل لاعب)
    // ──────────────────────────────────────────────

    /**
     * Individual token: verify then show form
     */
    public function show(string $token)
    {
        $link = VotingLink::where('token', $token)
            ->with(['campaign', 'player', 'club'])
            ->first();

        if (!$link) {
            return view('voting.invalid');
        }

        if ($link->status === 'used') {
            return view('voting.already-voted', ['player' => $link->player]);
        }

        if (!$link->isValid()) {
            return view('voting.expired', ['campaign' => $link->campaign]);
        }

        return view('voting.verify', [
            'token' => $token,
            'campaign' => $link->campaign,
            'club' => $link->club,
            'mode' => 'individual',
        ]);
    }

    public function verify(Request $request, string $token)
    {
        $request->validate([
            'national_id' => 'required|string',
        ], [
            'national_id.required' => 'يرجى إدخال رقم الهوية أو رقم الجوال.',
        ]);

        $link = VotingLink::where('token', $token)
            ->with(['campaign.questions.options', 'player', 'club'])
            ->firstOrFail();

        if (!$link->isValid()) {
            return redirect()->route('vote.show', $token);
        }

        $input = trim($request->input('national_id'));
        $player = $link->player;

        if ($player->national_id !== $input && $player->phone !== $input) {
            return back()->withErrors(['national_id' => 'بيانات التحقق غير صحيحة. تأكد من رقم الهوية أو رقم الجوال.']);
        }

        $campaign = $link->campaign;
        $questions = $campaign->questions()->active()->with('options')->orderBy('sort_order')->get();

        return view('voting.form', compact('link', 'campaign', 'questions', 'token'));
    }

    // ──────────────────────────────────────────────
    //  Review & Submit (مشترك بين النوعين)
    // ──────────────────────────────────────────────

    public function review(Request $request, string $token)
    {
        $link = VotingLink::where('token', $token)
            ->with(['campaign.questions.options', 'player'])
            ->firstOrFail();

        if (!$link->isValid()) {
            return redirect()->route('vote.show', $token);
        }

        if (!$link->campaign->allow_review_before_submit) {
            return $this->submit($request, $token);
        }

        $campaign = $link->campaign;
        $questions = $campaign->questions()->active()->with('options')->orderBy('sort_order')->get();

        $rules = $this->buildValidationRules($questions);
        $request->validate($rules);
        $answers = $request->input('answers', []);

        $reviewData = [];
        foreach ($questions as $question) {
            $answer = $answers[$question->id] ?? null;
            $displayAnswer = '';

            if ($question->type === 'checkbox' && is_array($answer)) {
                $displayAnswer = $question->options->whereIn('id', $answer)->pluck('label')->implode('، ');
            } elseif ($answer) {
                $option = $question->options->firstWhere('id', $answer);
                $displayAnswer = $option ? $option->label : $answer;
            }

            $reviewData[] = [
                'question' => $question,
                'answer' => $answer,
                'display' => $displayAnswer ?: 'لم تتم الإجابة',
            ];
        }

        return view('voting.review', compact('link', 'campaign', 'reviewData', 'answers'));
    }

    public function submit(Request $request, string $token)
    {
        $link = VotingLink::where('token', $token)
            ->with('campaign.questions')
            ->firstOrFail();

        if (!$link->isValid()) {
            return redirect()->route('vote.show', $token);
        }

        $questions = $link->campaign->questions()->active()->get();
        $request->validate($this->buildValidationRules($questions));

        $this->votingService->submitVote(
            $link,
            $request->input('answers', []),
            $request->ip(),
            $request->userAgent()
        );

        return view('voting.success', ['player' => $link->player, 'campaign' => $link->campaign]);
    }

    private function buildValidationRules($questions): array
    {
        $rules = [];
        foreach ($questions as $question) {
            $key = "answers.{$question->id}";
            if ($question->is_required) {
                $rules[$key] = 'required';
            }
            if ($question->type === 'checkbox' && $question->max_selections) {
                $rules[$key] = ($question->is_required ? 'required|' : 'nullable|') . "array|max:{$question->max_selections}";
            }
        }
        return $rules;
    }
}
