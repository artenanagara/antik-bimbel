<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentTryout extends Model
{
    protected $fillable = [
        'student_id',
        'tryout_id',
        'attempt_number',
        'status',
        'started_at',
        'submitted_at',
        'duration_seconds',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function tryout(): BelongsTo
    {
        return $this->belongsTo(Tryout::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(TryoutResult::class);
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function getRemainingSeconds(): int
    {
        if ($this->status !== 'in_progress' || !$this->started_at) {
            return 0;
        }
        $elapsed = max(0, now()->timestamp - $this->started_at->timestamp);
        $total = $this->tryout->duration_minutes * 60;
        return max(0, $total - $elapsed);
    }
}
