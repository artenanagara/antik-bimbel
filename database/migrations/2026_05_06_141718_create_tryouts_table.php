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
        Schema::create('tryouts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['simulation', 'sub_test']); // simulation = full SKD, sub_test = single sub test
            $table->enum('sub_test', ['TWK', 'TIU', 'TKP'])->nullable(); // only for sub_test type
            $table->unsignedSmallInteger('duration_minutes')->default(100);
            $table->unsignedSmallInteger('total_questions')->default(110);
            // SKD composition (for simulation type)
            $table->unsignedTinyInteger('twk_count')->default(30);
            $table->unsignedTinyInteger('tiu_count')->default(35);
            $table->unsignedTinyInteger('tkp_count')->default(45);
            // Passing grades
            $table->unsignedSmallInteger('pg_twk')->default(65);
            $table->unsignedSmallInteger('pg_tiu')->default(80);
            $table->unsignedSmallInteger('pg_tkp')->default(166);
            // Repeat settings
            $table->enum('repeat_allowed', ['unlimited', '1', '3', 'none'])->default('none');
            // Status
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryouts');
    }
};
