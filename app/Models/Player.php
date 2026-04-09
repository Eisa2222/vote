<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'club_id',
        'name',
        'national_id',
        'player_number',
        'phone',
        'email',
        'nationality',
        'position',
        'photo',
        'status',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function votingLinks()
    {
        return $this->hasMany(VotingLink::class);
    }

    public function votingResponses()
    {
        return $this->hasMany(VotingResponse::class);
    }

    public function votingSessions()
    {
        return $this->hasMany(VotingSession::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }
}
