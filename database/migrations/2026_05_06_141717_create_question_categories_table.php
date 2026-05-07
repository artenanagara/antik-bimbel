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
        Schema::create('question_categories', function (Blueprint $table) {
            $table->id();
            $table->enum('sub_test', ['TWK', 'TIU', 'TKP']);
            $table->string('name'); // e.g. Nasionalisme, Deret Angka, Pelayanan Publik
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_categories');
    }
};
