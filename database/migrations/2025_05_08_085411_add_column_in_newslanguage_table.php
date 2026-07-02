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
        Schema::table('news_languages', function (Blueprint $table) {
            $table->integer('position')->default(0)->after('code');;
        });

        $position = 1;
        foreach (\App\Models\NewsLanguage::all() as $language) {
            $language->position = $position++;
            $language->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_languages', function (Blueprint $table) {
            if (Schema::hasColumn('news_languages', 'position')) {
                $table->dropColumn('position');
            }
        });
    }
};
