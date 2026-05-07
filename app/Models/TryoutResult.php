<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TryoutResult extends Model
{
    protected $fillable = [
        'student_tryout_id',
        'student_id',
        'tryout_id',
        'attempt_number',
        'twk_score',
        'tiu_score',
        'tkp_score',
        'total_score',
        'twk_correct',
        'tiu_correct',
        'tkp_answered',
        'total_answered',
        'pass_twk',
        'pass_tiu',
        'pass_tkp',
        'pass_overall',
    ];

    protected function casts(): array
    {
        return [
            'pass_twk' => 'boolean',
            'pass_tiu' => 'boolean',
            'pass_tkp' => 'boolean',
            'pass_overall' => 'boolean',
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

    public function studentTryout(): BelongsTo
    {
        return $this->belongsTo(StudentTryout::class);
    }

    public function getPassStatusLabel(): string
    {
        if ($this->pass_overall) {
            return 'Lulus Semua';
        }
        $failed = [];
        if (!$this->pass_twk) $failed[] = 'TWK';
        if (!$this->pass_tiu) $failed[] = 'TIU';
        if (!$this->pass_tkp) $failed[] = 'TKP';
        return 'Tidak Lulus ' . implode(', ', $failed);
    }
}
