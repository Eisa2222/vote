<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignWorkflow extends Model
{
    protected $fillable = [
        'campaign_id', 'user_id', 'from_status', 'to_status', 'action', 'comment',
    ];

    public function campaign()
    {
        return $this->belongsTo(VotingCampaign::class, 'campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'submit_for_review' => 'تقديم للمراجعة',
            'approve_review' => 'اعتماد المراجعة',
            'reject_review' => 'رفض المراجعة',
            'approve_final' => 'الاعتماد النهائي',
            'reject_final' => 'رفض الاعتماد',
            'send' => 'إرسال للأندية',
            default => $this->action,
        };
    }
}
