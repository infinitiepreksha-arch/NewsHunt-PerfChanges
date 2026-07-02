<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->tinyInteger('is_short_video')
                ->nullable()
                ->default(0)
                ->comment('0 = No, 1 = Yes')
                ->after('is_video');
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'is_short_video')) {
                $table->dropColumn('is_short_video');
            }
        });
    }

};
