<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingResponse extends Model
{
    protected $fillable = [
        'session_id',
        'campaign_id',
        'player_id',
        'club_id',
    ];

    public function session()
    {
        return $this->belongsTo(VotingSession::class, 'session_id');
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

    public function answers()
    {
        return $this->hasMany(VotingResponseAnswer::class, 'response_id');
    }
}
