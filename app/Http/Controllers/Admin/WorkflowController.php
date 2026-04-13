<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CampaignWorkflow;
use App\Models\VotingCampaign;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    /**
     * Dashboard: campaigns pending action for current user's role
     */
    public function index()
    {
        $user = auth()->user();

        $pendingReview = VotingCampaign::where('workflow_status', 'pending_review')
            ->withCount('questions')
            ->latest()
            ->get();

        $pendingApproval = VotingCampaign::where('workflow_status', 'pending_approval')
            ->withCount('questions')
            ->latest()
            ->get();

        $recentActions = CampaignWorkflow::with(['campaign', 'user'])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.workflow.index', compact('pendingReview', 'pendingApproval', 'recentActions'));
    }

    /**
     * View campaign details for workflow action
     */
    public function show(VotingCampaign $campaign)
    {
        $campaign->load(['questions.options', 'targets.club', 'creator', 'submitter', 'reviewer', 'approver']);
        $history = $campaign->workflowHistory()->with('user')->get();

        return view('admin.workflow.show', compact('campaign', 'history'));
    }

    /**
     * Creator submits campaign for review
     */
    public function submitForReview(VotingCampaign $campaign)
    {
        if ($campaign->workflow_status !== 'draft' && $campaign->workflow_status !== 'rejected') {
            return back()->with('error', 'لا يمكن تقديم هذه الحملة للمراجعة.');
        }

        if ($campaign->questions()->count() === 0) {
            return back()->with('error', 'لا يمكن تقديم حملة بدون أسئلة.');
        }

        $this->transition($campaign, 'pending_review', 'submit_for_review');
        $campaign->update([
            'submitted_by' => auth()->id(),
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'تم تقديم الحملة للمراجعة.');
    }

    /**
     * Reviewer approves the review
     */
    public function approveReview(Request $request, VotingCampaign $campaign)
    {
        if ($campaign->workflow_status !== 'pending_review') {
            return back()->with('error', 'الحملة ليست بانتظار المراجعة.');
        }

        $this->transition($campaign, 'pending_approval', 'approve_review', $request->input('comment'));
        $campaign->update([
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'تمت المراجعة. الحملة الآن بانتظار الاعتماد النهائي.');
    }

    /**
     * Reviewer rejects the campaign
     */
    public function rejectReview(Request $request, VotingCampaign $campaign)
    {
        $request->validate(['comment' => 'required|string|max:1000'], [
            'comment.required' => 'يرجى كتابة سبب الرفض.',
        ]);

        if ($campaign->workflow_status !== 'pending_review') {
            return back()->with('error', 'الحملة ليست بانتظار المراجعة.');
        }

        $this->transition($campaign, 'rejected', 'reject_review', $request->input('comment'));

        return back()->with('success', 'تم رفض الحملة وإعادتها للمنشئ.');
    }

    /**
     * Approver gives final approval
     */
    public function approveFinal(Request $request, VotingCampaign $campaign)
    {
        if ($campaign->workflow_status !== 'pending_approval') {
            return back()->with('error', 'الحملة ليست بانتظار الاعتماد.');
        }

        $this->transition($campaign, 'approved', 'approve_final', $request->input('comment'));
        $campaign->update([
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'status' => 'active',
        ]);

        return back()->with('success', 'تم اعتماد الحملة. يمكن الآن إرسالها للأندية.');
    }

    /**
     * Approver rejects final approval
     */
    public function rejectFinal(Request $request, VotingCampaign $campaign)
    {
        $request->validate(['comment' => 'required|string|max:1000'], [
            'comment.required' => 'يرجى كتابة سبب الرفض.',
        ]);

        if ($campaign->workflow_status !== 'pending_approval') {
            return back()->with('error', 'الحملة ليست بانتظار الاعتماد.');
        }

        $this->transition($campaign, 'rejected', 'reject_final', $request->input('comment'));

        return back()->with('success', 'تم رفض الحملة.');
    }

    private function transition(VotingCampaign $campaign, string $toStatus, string $action, ?string $comment = null): void
    {
        CampaignWorkflow::create([
            'campaign_id' => $campaign->id,
            'user_id' => auth()->id(),
            'from_status' => $campaign->workflow_status,
            'to_status' => $toStatus,
            'action' => $action,
            'comment' => $comment,
        ]);

        $campaign->update(['workflow_status' => $toStatus]);

        ActivityLog::log($action, $campaign, "Workflow: {$campaign->workflow_status_label}");
    }
}
