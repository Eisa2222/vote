<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CampaignAssignment extends Model
{
    protected $fillable = [
        'campaign_id',
        'admin_id',
        'club_id',
        'shared_token',
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

    protected static function booted(): void
    {
        static::creating(function (CampaignAssignment $assignment) {
            if (!$assignment->shared_token) {
                $assignment->shared_token = Str::random(32);
            }
        });
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

    public function getSharedUrlAttribute(): string
    {
        return route('vote.shared', $this->shared_token);
    }
}
