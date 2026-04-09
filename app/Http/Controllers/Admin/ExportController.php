<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VotingCampaign;
use App\Models\VotingResponse;
use App\Models\VotingResponseAnswer;
use App\Services\VotingService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function __construct(private VotingService $votingService) {}

    public function campaignResultsCsv(VotingCampaign $campaign)
    {
        $campaign->load('questions.options');

        $headers = ['اللاعب', 'النادي', 'تاريخ التصويت'];
        foreach ($campaign->questions as $q) {
            $headers[] = $q->title;
        }

        $responses = VotingResponse::where('campaign_id', $campaign->id)
            ->with(['player', 'club', 'answers.option', 'answers.question'])
            ->get();

        $callback = function () use ($responses, $campaign, $headers) {
            $file = fopen('php://output', 'w');
            // BOM for Excel Arabic support
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $headers);

            foreach ($responses as $response) {
                $row = [
                    $response->player->name ?? '',
                    $response->club->name_ar ?? '',
                    $response->created_at->format('Y-m-d H:i'),
                ];

                foreach ($campaign->questions as $question) {
                    $qAnswers = $response->answers->where('question_id', $question->id);
                    $answerTexts = $qAnswers->map(function ($a) {
                        return $a->option ? $a->option->label : $a->text_answer;
                    })->implode('، ');
                    $row[] = $answerTexts;
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        $filename = 'campaign_results_' . $campaign->id . '_' . date('Y-m-d') . '.csv';

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function campaignResultsPdf(VotingCampaign $campaign)
    {
        $campaign->load('questions.options');
        $stats = $this->votingService->getCampaignStats($campaign);

        $questionResults = [];
        foreach ($campaign->questions as $question) {
            $questionResults[$question->id] = $this->votingService->getQuestionResults($question->id);
        }

        $pdf = Pdf::loadView('admin.exports.campaign-pdf', compact('campaign', 'stats', 'questionResults'));
        $pdf->setPaper('a4');

        return $pdf->download('campaign_report_' . $campaign->id . '.pdf');
    }

    public function participationCsv(VotingCampaign $campaign)
    {
        $links = $campaign->votingLinks()->with(['player', 'club'])->get();

        $callback = function () use ($links) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['اللاعب', 'النادي', 'الحالة', 'تاريخ الإرسال', 'تاريخ التصويت']);

            foreach ($links as $link) {
                fputcsv($file, [
                    $link->player->name ?? '',
                    $link->club->name_ar ?? '',
                    match ($link->status) {
                        'pending' => 'في الانتظار',
                        'sent' => 'تم الإرسال',
                        'used' => 'تم التصويت',
                        'expired' => 'منتهي',
                    },
                    $link->sent_at?->format('Y-m-d H:i') ?? '-',
                    $link->used_at?->format('Y-m-d H:i') ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="participation_' . $campaign->id . '.csv"',
        ]);
    }
}
