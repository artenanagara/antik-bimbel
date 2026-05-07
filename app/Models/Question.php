<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    protected $fillable = [
        'code',
        'sub_test',
        'category_id',
        'question_text',
        'question_image',
        'explanation',
        'difficulty',
        'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class, 'category_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('label');
    }

    public function tryouts(): BelongsToMany
    {
        return $this->belongsToMany(Tryout::class, 'tryout_questions')
            ->withPivot('order')
            ->withTimestamps();
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Question $question) {
            if (empty($question->code)) {
                $prefix = $question->sub_test;
                $count = static::where('sub_test', $prefix)->count() + 1;
                $question->code = $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
