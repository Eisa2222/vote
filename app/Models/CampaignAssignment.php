<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignAssignment extends Model
{
    protected $fillable = [
        'campaign_id',
        'admin_id',
        'club_id',
        'status',
        'sent_at',
        'delivered_at',
        'opened_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'opened_at' => 'datetime',
        ];
    }

    public function campaign()
    {
        return $this->belongsTo(VotingCampaign::class, 'campaign_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
