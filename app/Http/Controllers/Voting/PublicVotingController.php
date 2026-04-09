<?php

namespace App\Http\Controllers\Voting;

use App\Http\Controllers\Controller;
use App\Models\VotingLink;
use App\Models\VotingQuestion;
use App\Services\VotingService;
use Illuminate\Http\Request;

class PublicVotingController extends Controller
{
    public function __construct(private VotingService $votingService) {}

    public function show(string $token)
    {
        $link = VotingLink::where('token', $token)
            ->with(['campaign.questions.options', 'player', 'club'])
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

        $campaign = $link->campaign;
        $questions = $campaign->questions()->active()->with('options')->orderBy('sort_order')->get();

        return view('voting.form', compact('link', 'campaign', 'questions'));
    }

    public function review(Request $request, string $token)
    {
        $link = VotingLink::where('token', $token)
            ->with(['campaign.questions.options', 'player'])
            ->firstOrFail();

        if (!$link->isValid()) {
            return redirect()->route('vote.show', $token);
        }

        $campaign = $link->campaign;
        $questions = $campaign->questions()->active()->with('options')->orderBy('sort_order')->get();

        // Validate answers
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

        $validated = $request->validate($rules);
        $answers = $request->input('answers', []);

        // Build review data
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
                'display' => $displayAnswer,
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

        // Validate
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

        $request->validate($rules);

        $this->votingService->submitVote(
            $link,
            $request->input('answers', []),
            $request->ip(),
            $request->userAgent()
        );

        return view('voting.success', ['player' => $link->player, 'campaign' => $link->campaign]);
    }
}
