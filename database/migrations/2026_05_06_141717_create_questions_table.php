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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. TWK-0001
            $table->enum('sub_test', ['TWK', 'TIU', 'TKP']);
            $table->foreignId('category_id')->nullable()->constrained('question_categories')->nullOnDelete();
            $table->longText('question_text');
            $table->string('question_image')->nullable();
            $table->longText('explanation')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->enum('status', ['active', 'draft'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
