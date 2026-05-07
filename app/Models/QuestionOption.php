<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    protected $fillable = ['question_id', 'label', 'text', 'image', 'score', 'is_correct'];

    protected function casts(): array
    {
        return ['is_correct' => 'boolean', 'score' => 'integer'];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
