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
        Schema::table('e_newspapers', function (Blueprint $table) {
            $table->unsignedBigInteger('topic_id')->nullable()->after('news_language_id');
            $table->string('background_image')
                ->nullable()
                ->after('topic_id');
            $table->foreign('topic_id')->references('id')->on('topics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('e_newspapers', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->dropColumn(['topic_id', 'background_image']);
        });
    }
};
