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
        Schema::create('active_user_counts', function (Blueprint $table) {
            $table->id();
            $table->date('date');     // store date (e.g. 2025-09-03)
            $table->time('time');     // store time (e.g. 14:30:00)
            $table->integer('count'); // active users count
            $table->timestamps();     // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_user_counts');
    }
};
