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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('channel_id');
            $table->unsignedBigInteger('topic_id');
            $table->string('title')->unique();
            $table->string('resource');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('description');
            $table->string('status');
            $table->string('pubdate');
            $table->unsignedBigInteger('view_count');
            $table->unsignedBigInteger('reaction');
            $table->unsignedBigInteger('shere');
            $table->unsignedBigInteger('comment');
            $table->unsignedBigInteger('favorite');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
