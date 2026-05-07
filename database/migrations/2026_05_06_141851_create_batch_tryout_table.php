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
        Schema::create('batch_tryout', function (Blueprint $table) {
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tryout_id')->constrained()->cascadeOnDelete();
            $table->primary(['batch_id', 'tryout_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_tryout');
    }
};
