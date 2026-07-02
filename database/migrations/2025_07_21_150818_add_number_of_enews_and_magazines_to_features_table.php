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
        Schema::table('features', function (Blueprint $table) {
            $table->integer('number_of_e_papers_and_magazines')->default(0)->after('number_of_stories');

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('features', function (Blueprint $table) {
            $table->dropColumn('number_of_e_papers_and_magazines');
        });
    }
};
