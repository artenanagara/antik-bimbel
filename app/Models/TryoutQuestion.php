<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TryoutQuestion extends Model
{
    protected $fillable = ['tryout_id', 'question_id', 'order'];

    public function tryout(): BelongsTo
    {
        return $this->belongsTo(Tryout::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
