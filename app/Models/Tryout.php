<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tryout extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'sub_test',
        'duration_minutes',
        'total_questions',
        'twk_count',
        'tiu_count',
        'tkp_count',
        'pg_twk',
        'pg_tiu',
        'pg_tkp',
        'repeat_allowed',
        'status',
        'start_at',
        'end_at',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'tryout_questions')
            ->withPivot('order')
            ->withTimestamps()
            ->orderByPivot('order');
    }

    public function batches(): BelongsToMany
    {
        return $this->belongsToMany(Batch::class);
    }

    public function studentTryouts(): HasMany
    {
        return $this->hasMany(StudentTryout::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(TryoutResult::class);
    }

    public function isSimulation(): bool
    {
        return $this->type === 'simulation';
    }

    public function getRepeatLimitAttribute(): ?int
    {
        return match ($this->repeat_allowed) {
            'unlimited' => null,
            '1' => 1,
            '3' => 3,
            default => 0,
        };
    }
}
