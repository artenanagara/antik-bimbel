<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionCategory extends Model
{
    protected $fillable = ['sub_test', 'name'];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'category_id');
    }
}
