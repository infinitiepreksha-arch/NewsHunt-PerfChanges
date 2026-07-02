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
        Schema::table('posts', function (Blueprint $table) {
            $table->enum('type', ['post', 'video'])->after("slug");
            $table->string('video_thumb')->nullable()->after("image");
            $table->text('video')->nullable()->after("video_thumb");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->enum('type', ['post', 'video'])->after("slug");
            $table->string('video_thumb')->nullable()->after("image");
            $table->text('video')->nullable()->after("video_thumb");
        });
    }
};
