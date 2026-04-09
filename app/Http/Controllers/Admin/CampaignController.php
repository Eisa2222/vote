<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CampaignAssignment;
use App\Models\Club;
use App\Models\User;
use App\Models\VotingCampaign;
use App\Models\VotingCampaignTarget;
use App\Models\VotingQuestion;
use App\Models\VotingQuestionOption;
use App\Services\VotingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    public function __construct(private VotingService $votingService) {}

    public function index()
    {
        $campaigns = VotingCampaign::withCount(['questions', 'responses', 'votingLinks'])
            ->latest()
            ->paginate(15);

        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $clubs = Club::active()->get();
        return view('admin.campaigns.create', compact('clubs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'target_audience' => 'required|in:all,specific',
            'target_clubs' => 'required_if:target_audience,specific|array',
            'target_clubs.*' => 'exists:clubs,id',
            'results_visible_after' => 'nullable|date',
        ]);

        $validated['status'] = $request->input('save_as_draft') ? 'draft' : 'active';
        $validated['created_by'] = auth()->id();
        $validated['single_response'] = $request->has('single_response');
        $validated['prevent_edit_after_submit'] = $request->has('prevent_edit_after_submit');
        $validated['show_countdown'] = $request->has('show_countdown');
        $validated['allow_review_before_submit'] = $request->has('allow_review_before_submit');

        $campaign = DB::transaction(function () use ($validated, $request) {
            $targetClubs = $validated['target_clubs'] ?? [];
            unset($validated['target_clubs']);

            $campaign = VotingCampaign::create($validated);

            if ($validated['target_audience'] === 'all') {
                $allClubs = Club::active()->pluck('id');
                foreach ($allClubs as $clubId) {
                    VotingCampaignTarget::create([
                        'campaign_id' => $campaign->id,
                        'club_id' => $clubId,
                    ]);
                }
            } else {
                foreach ($targetClubs as $clubId) {
                    VotingCampaignTarget::create([
                        'campaign_id' => $campaign->id,
                        'club_id' => $clubId,
                    ]);
                }
            }

            return $campaign;
        });

        ActivityLog::log('create', $campaign, 'تم إنشاء حملة التصويت: ' . $campaign->title);

        return redirect()->route('admin.campaigns.questions', $campaign)->with('success', 'تم إنشاء الحملة بنجاح. أضف الأسئلة الآن.');
    }

    public function edit(VotingCampaign $campaign)
    {
        $clubs = Club::active()->get();
        $targetClubIds = $campaign->targets->pluck('club_id')->toArray();
        return view('admin.campaigns.edit', compact('campaign', 'clubs', 'targetClubIds'));
    }

    public function update(Request $request, VotingCampaign $campaign)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|in:draft,scheduled,active,closed,archived',
            'target_audience' => 'required|in:all,specific',
            'target_clubs' => 'required_if:target_audience,specific|array',
            'target_clubs.*' => 'exists:clubs,id',
            'results_visible_after' => 'nullable|date',
        ]);

        $old = $campaign->toArray();
        $validated['single_response'] = $request->has('single_response');
        $validated['prevent_edit_after_submit'] = $request->has('prevent_edit_after_submit');
        $validated['show_countdown'] = $request->has('show_countdown');
        $validated['allow_review_before_submit'] = $request->has('allow_review_before_submit');

        DB::transaction(function () use ($campaign, $validated) {
            $targetClubs = $validated['target_clubs'] ?? [];
            unset($validated['target_clubs']);

            $campaign->update($validated);

            // Update targets
            $campaign->targets()->delete();
            if ($validated['target_audience'] === 'all') {
                foreach (Club::active()->pluck('id') as $clubId) {
                    VotingCampaignTarget::create(['campaign_id' => $campaign->id, 'club_id' => $clubId]);
                }
            } else {
                foreach ($targetClubs as $clubId) {
                    VotingCampaignTarget::create(['campaign_id' => $campaign->id, 'club_id' => $clubId]);
                }
            }
        });

        ActivityLog::log('update', $campaign, 'تم تحديث حملة التصويت: ' . $campaign->title, $old, $campaign->toArray());

        return redirect()->route('admin.campaigns.index')->with('success', 'تم تحديث الحملة بنجاح');
    }

    public function destroy(VotingCampaign $campaign)
    {
        ActivityLog::log('delete', $campaign, 'تم حذف حملة التصويت: ' . $campaign->title);
        $campaign->delete();
        return redirect()->route('admin.campaigns.index')->with('success', 'تم حذف الحملة بنجاح');
    }

    public function questions(VotingCampaign $campaign)
    {
        $campaign->load('questions.options');
        return view('admin.campaigns.questions', compact('campaign'));
    }

    public function storeQuestion(Request $request, VotingCampaign $campaign)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:radio,checkbox,text,select,rating,yesno',
            'max_selections' => 'nullable|integer|min:1',
            'options' => 'required_unless:type,text|array|min:2',
            'options.*.label' => 'required|string|max:255',
        ]);

        $validated['is_required'] = $request->has('is_required');
        $validated['sort_order'] = $campaign->questions()->max('sort_order') + 1;

        $question = DB::transaction(function () use ($campaign, $validated) {
            $options = $validated['options'] ?? [];
            unset($validated['options']);

            $question = $campaign->questions()->create($validated);

            foreach ($options as $i => $option) {
                $question->options()->create([
                    'label' => $option['label'],
                    'value' => 'opt_' . ($i + 1),
                    'sort_order' => $i + 1,
                ]);
            }

            return $question;
        });

        return redirect()->route('admin.campaigns.questions', $campaign)->with('success', 'تم إضافة السؤال بنجاح');
    }

    public function updateQuestion(Request $request, VotingCampaign $campaign, VotingQuestion $question)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:radio,checkbox,text,select,rating,yesno',
            'max_selections' => 'nullable|integer|min:1',
            'options' => 'required_unless:type,text|array|min:2',
            'options.*.label' => 'required|string|max:255',
        ]);

        $validated['is_required'] = $request->has('is_required');

        DB::transaction(function () use ($question, $validated) {
            $options = $validated['options'] ?? [];
            unset($validated['options']);

            $question->update($validated);
            $question->options()->delete();

            foreach ($options as $i => $option) {
                $question->options()->create([
                    'label' => $option['label'],
                    'value' => 'opt_' . ($i + 1),
                    'sort_order' => $i + 1,
                ]);
            }
        });

        return redirect()->route('admin.campaigns.questions', $campaign)->with('success', 'تم تحديث السؤال بنجاح');
    }

    public function destroyQuestion(VotingCampaign $campaign, VotingQuestion $question)
    {
        $question->delete();
        return redirect()->route('admin.campaigns.questions', $campaign)->with('success', 'تم حذف السؤال بنجاح');
    }

    public function reorderQuestions(Request $request, VotingCampaign $campaign)
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $index => $questionId) {
            VotingQuestion::where('id', $questionId)->where('campaign_id', $campaign->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    public function send(VotingCampaign $campaign)
    {
        $campaign->load('targets.club');
        $admins = User::role('club-admin')
            ->whereIn('club_id', $campaign->targets->pluck('club_id'))
            ->with('club')
            ->get();

        $existingAssignments = $campaign->assignments()->pluck('admin_id')->toArray();

        return view('admin.campaigns.send', compact('campaign', 'admins', 'existingAssignments'));
    }

    public function sendToAdmins(Request $request, VotingCampaign $campaign)
    {
        $request->validate([
            'admin_ids' => 'required|array',
            'admin_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->admin_ids as $adminId) {
            $admin = User::find($adminId);
            CampaignAssignment::firstOrCreate(
                ['campaign_id' => $campaign->id, 'admin_id' => $adminId],
                [
                    'club_id' => $admin->club_id,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]
            );
        }

        if ($campaign->status === 'draft') {
            $campaign->update(['status' => 'active']);
        }

        ActivityLog::log('send', $campaign, 'تم إرسال الحملة إلى ' . count($request->admin_ids) . ' إداري');

        return redirect()->route('admin.campaigns.index')->with('success', 'تم إرسال الحملة للإداريين بنجاح');
    }

    public function results(VotingCampaign $campaign)
    {
        $campaign->load('questions.options');
        $stats = $this->votingService->getCampaignStats($campaign);

        $questionResults = [];
        foreach ($campaign->questions as $question) {
            $questionResults[$question->id] = $this->votingService->getQuestionResults($question->id);
        }

        $clubStats = [];
        foreach ($campaign->targets as $target) {
            $clubStats[$target->club_id] = [
                'club' => $target->club,
                'stats' => $this->votingService->getCampaignStats($campaign, $target->club_id),
            ];
        }

        return view('admin.campaigns.results', compact('campaign', 'stats', 'questionResults', 'clubStats'));
    }

    public function tracking(VotingCampaign $campaign)
    {
        $assignments = $campaign->assignments()->with(['admin', 'club'])->get();
        $links = $campaign->votingLinks()->with(['player', 'club'])->paginate(50);

        return view('admin.campaigns.tracking', compact('campaign', 'assignments', 'links'));
    }
}
