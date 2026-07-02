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
        Schema::table('rss_feeds', function (Blueprint $table) {
            $table->enum('description_type', ['description-tag', 'content-encoded', 'media:description'])
                ->nullable()
                ->after('feed_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('rss_feeds', function (Blueprint $table) {
            if (Schema::hasColumn('rss_feeds', 'description_type')) {
                $table->dropColumn('description_type');
            }
        });
    }
};
