<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'league_id',
        'name_ar',
        'name_en',
        'logo',
        'contact_email',
        'contact_phone',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class)->whereHas('roles', fn($q) => $q->where('name', 'club-admin'));
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function campaignTargets()
    {
        return $this->hasMany(VotingCampaignTarget::class);
    }

    public function votingLinks()
    {
        return $this->hasMany(VotingLink::class);
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : ($this->name_en ?: $this->name_ar);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
