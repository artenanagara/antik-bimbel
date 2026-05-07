<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    protected $fillable = ['student_tryout_id', 'question_id', 'option_id', 'is_flagged', 'score'];

    protected function casts(): array
    {
        return ['is_flagged' => 'boolean', 'score' => 'integer'];
    }

    public function studentTryout(): BelongsTo
    {
        return $this->belongsTo(StudentTryout::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'option_id');
    }
}
