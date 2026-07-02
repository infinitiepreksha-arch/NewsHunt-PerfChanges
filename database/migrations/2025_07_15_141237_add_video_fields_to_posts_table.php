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
            $table->text('video_url')->nullable()->after('resource');
            $table->text('video_embed')->nullable()->after('video_url');
            $table->string('video_type')->nullable()->after('video_embed');
            $table->boolean('is_video')->default(false)->after('video_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'video_embed', 'video_type', 'is_video']);
        });
    }
};
