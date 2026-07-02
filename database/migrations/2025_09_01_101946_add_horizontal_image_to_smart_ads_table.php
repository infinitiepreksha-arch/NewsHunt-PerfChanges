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
        Schema::table('smart_ads', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->string('vertical_image')->nullable()->after('id');
            $table->string('horizontal_image')->nullable()->after('vertical_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smart_ads', function (Blueprint $table) {
            $table->dropColumn(['vertical_image', 'horizontal_image']);
            $table->string('image')->nullable()->after('id');
        });
    }
};
