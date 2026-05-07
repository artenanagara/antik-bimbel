<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->enum('label', ['A', 'B', 'C', 'D', 'E']);
            $table->longText('text');
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('score')->default(0); // For TKP: 1-5; for TWK/TIU: 5 for correct answer, 0 otherwise
            $table->boolean('is_correct')->default(false); // Used for TWK/TIU
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
