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
        $tables = ['rss_feeds', 'posts', 'channels'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedBigInteger('news_language_id')->nullable()->after('id');
                $table->foreign('news_language_id')->references('id')->on('news_languages')->onDelete('cascade');
            });
        }
        
    }
    
    public function down(): void
    {
        $tables = ['rss_feeds', 'posts', 'channels'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                // Manually specify the correct foreign key name
                $foreignKeyName = $table . '_news_language_id_foreign';
                
                if (Schema::hasColumn($table, 'news_language_id')) {
                    $tableBlueprint->dropForeign($foreignKeyName);
                    $tableBlueprint->dropColumn('news_language_id');
                }
            });
        }
          
    }
};
