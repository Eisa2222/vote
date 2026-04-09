<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingSession extends Model
{
    protected $fillable = [
        'voting_link_id',
        'campaign_id',
        'player_id',
        'club_id',
        'ip_address',
        'user_agent',
        'started_at',
        'submitted_at',
        'is_completed',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'is_completed' => 'boolean',
        ];
    }

    public function votingLink()
    {
        return $this->belongsTo(VotingLink::class);
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

    public function response()
    {
        return $this->hasOne(VotingResponse::class, 'session_id');
    }
}
