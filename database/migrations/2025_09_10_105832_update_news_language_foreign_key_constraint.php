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
            // Drop the existing foreign key constraint if it exists
            $foreignKeyName = 'news_language_id';
            if (Schema::hasColumn('posts', 'news_language_id') && Schema::hasTable('posts')) {
                $table->dropForeign([$foreignKeyName]);
            }

            // Add the new foreign key constraint with onDelete('set null')
            $table->foreign('news_language_id')->references('id')->on('news_languages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Drop the new foreign key constraint if it exists
            $foreignKeyName = 'news_language_id';
            if (Schema::hasColumn('posts', 'news_language_id') && Schema::hasTable('posts')) {
                $table->dropForeign([$foreignKeyName]);
            }

            // Re-add the original foreign key constraint with onDelete('cascade')
            $table->foreign('news_language_id')->references('id')->on('news_languages')->onDelete('cascade');
        });
    }
};
