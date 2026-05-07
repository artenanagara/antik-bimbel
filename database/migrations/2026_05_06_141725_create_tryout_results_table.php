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
        Schema::create('tryout_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_tryout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tryout_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->unsignedSmallInteger('twk_score')->default(0);
            $table->unsignedSmallInteger('tiu_score')->default(0);
            $table->unsignedSmallInteger('tkp_score')->default(0);
            $table->unsignedSmallInteger('total_score')->default(0);
            $table->unsignedTinyInteger('twk_correct')->default(0);
            $table->unsignedTinyInteger('tiu_correct')->default(0);
            $table->unsignedTinyInteger('tkp_answered')->default(0);
            $table->unsignedTinyInteger('total_answered')->default(0);
            $table->boolean('pass_twk')->default(false);
            $table->boolean('pass_tiu')->default(false);
            $table->boolean('pass_tkp')->default(false);
            $table->boolean('pass_overall')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryout_results');
    }
};
