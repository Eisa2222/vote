<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VotingCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'starts_at', 'ends_at',
        'status', 'workflow_status', 'voting_type', 'target_audience',
        'single_response', 'prevent_edit_after_submit', 'show_countdown',
        'results_visible_after', 'allow_review_before_submit',
        'created_by', 'submitted_by', 'reviewed_by', 'approved_by',
        'submitted_at', 'reviewed_at', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'results_visible_after' => 'datetime',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'single_response' => 'boolean',
            'prevent_edit_after_submit' => 'boolean',
            'show_countdown' => 'boolean',
            'allow_review_before_submit' => 'boolean',
        ];
    }

    // Relationships
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function submitter() { return $this->belongsTo(User::class, 'submitted_by'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function questions() { return $this->hasMany(VotingQuestion::class, 'campaign_id')->orderBy('sort_order'); }
    public function targets() { return $this->hasMany(VotingCampaignTarget::class, 'campaign_id'); }
    public function targetClubs() { return $this->belongsToMany(Club::class, 'voting_campaign_targets', 'campaign_id', 'club_id'); }
    public function assignments() { return $this->hasMany(CampaignAssignment::class, 'campaign_id'); }
    public function votingLinks() { return $this->hasMany(VotingLink::class, 'campaign_id'); }
    public function responses() { return $this->hasMany(VotingResponse::class, 'campaign_id'); }
    public function sessions() { return $this->hasMany(VotingSession::class, 'campaign_id'); }
    public function workflowHistory() { return $this->hasMany(CampaignWorkflow::class, 'campaign_id')->latest(); }

    // Status helpers
    public function isActive(): bool { return $this->status === 'active'; }
    public function isExpired(): bool { return $this->ends_at && $this->ends_at->isPast(); }
    public function scopeActive($query) { return $query->where('status', 'active'); }

    public function getWorkflowStatusLabelAttribute(): string
    {
        return match ($this->workflow_status) {
            'draft' => 'مسودة',
            'pending_review' => 'بانتظار المراجعة',
            'reviewed' => 'تمت المراجعة',
            'pending_approval' => 'بانتظار الاعتماد',
            'approved' => 'معتمدة',
            'rejected' => 'مرفوضة',
            'sent' => 'تم الإرسال',
            default => $this->workflow_status,
        };
    }

    public function getWorkflowStatusColorAttribute(): string
    {
        return match ($this->workflow_status) {
            'draft' => 'secondary',
            'pending_review' => 'warning',
            'reviewed' => 'info',
            'pending_approval' => 'primary',
            'approved' => 'success',
            'rejected' => 'danger',
            'sent' => 'dark',
            default => 'secondary',
        };
    }
}
