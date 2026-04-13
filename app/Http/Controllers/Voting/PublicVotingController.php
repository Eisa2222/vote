<?php

namespace App\Http\Controllers\Voting;

use App\Http\Controllers\Controller;
use App\Models\VotingLink;
use App\Services\VotingService;
use Illuminate\Http\Request;

class PublicVotingController extends Controller
{
    public function __construct(private VotingService $votingService) {}

    /**
     * Step 1: Landing page - show verification form
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
        ]);
    }

    /**
     * Step 2: Verify identity then show voting form
     */
    public function verify(Request $request, string $token)
    {
        $request->validate([
            'national_id' => 'required|string',
        ], [
            'national_id.required' => 'يرجى إدخال رقم الهوية أو رقم الجوال للتحقق.',
        ]);

        $link = VotingLink::where('token', $token)
            ->with(['campaign.questions.options', 'player', 'club'])
            ->firstOrFail();

        if (!$link->isValid()) {
            return redirect()->route('vote.show', $token);
        }

        // Verify identity: match national_id OR phone
        $input = $request->input('national_id');
        $player = $link->player;

        if ($player->national_id !== $input && $player->phone !== $input) {
            return back()->withErrors(['national_id' => 'بيانات التحقق غير صحيحة. تأكد من رقم الهوية أو رقم الجوال.']);
        }

        $campaign = $link->campaign;
        $questions = $campaign->questions()->active()->with('options')->orderBy('sort_order')->get();

        return view('voting.form', compact('link', 'campaign', 'questions', 'token'));
    }

    /**
     * Step 3: Review answers before submission
     */
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
                $selectedOptions = $question->options->whereIn('id', $answer);
                $displayAnswer = $selectedOptions->pluck('label')->implode('، ');
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

    /**
     * Step 4: Submit vote
     */
    public function submit(Request $request, string $token)
    {
        $link = VotingLink::where('token', $token)
            ->with('campaign.questions')
            ->firstOrFail();

        if (!$link->isValid()) {
            return redirect()->route('vote.show', $token);
        }

        $questions = $link->campaign->questions()->active()->get();

        $rules = $this->buildValidationRules($questions);
        $request->validate($rules);

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
