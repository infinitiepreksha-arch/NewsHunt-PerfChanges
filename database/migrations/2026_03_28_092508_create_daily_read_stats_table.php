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
        Schema::create('daily_read_stats', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->date('date');

            $table->integer('article_read_count')->default(0);
            $table->integer('story_read_count')->default(0);
            $table->integer('epaper_read_count')->default(0);

            $table->integer('total_article_read_count')->default(0);
            $table->integer('total_story_read_count')->default(0);
            $table->integer('total_epaper_read_count')->default(0);

            $table->primary(['user_id', 'date']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_read_stats');
    }
};
