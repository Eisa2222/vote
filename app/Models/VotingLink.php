<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VotingLink extends Model
{
    protected $fillable = [
        'campaign_id',
        'player_id',
        'club_id',
        'generated_by',
        'token',
        'status',
        'sent_at',
        'expires_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function campaign()
    {
        return $this->belongsTo(VotingCampaign::class, 'campaign_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function session()
    {
        return $this->hasOne(VotingSession::class);
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function isValid(): bool
    {
        if ($this->status === 'used') return false;
        if ($this->status === 'expired') return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->campaign->isExpired()) return false;
        if (!$this->campaign->isActive()) return false;
        return true;
    }

    public function markAsUsed(): void
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
