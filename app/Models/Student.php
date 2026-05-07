<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = ['user_id', 'batch_id', 'full_name', 'address', 'phone'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function studentTryouts(): HasMany
    {
        return $this->hasMany(StudentTryout::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(TryoutResult::class);
    }

    public function getBestResult(int $tryoutId): ?TryoutResult
    {
        return $this->results()->where('tryout_id', $tryoutId)->orderByDesc('total_score')->first();
    }

    public function getLastResult(int $tryoutId): ?TryoutResult
    {
        return $this->results()->where('tryout_id', $tryoutId)->latest()->first();
    }
}
