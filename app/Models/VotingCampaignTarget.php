<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingCampaignTarget extends Model
{
    protected $fillable = ['campaign_id', 'club_id'];

    public function campaign()
    {
        return $this->belongsTo(VotingCampaign::class, 'campaign_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
