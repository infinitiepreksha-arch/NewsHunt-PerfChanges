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
        Schema::create('e_newspapers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('channel_id');
            $table->unsignedBigInteger('news_language_id');
            $table->date('date');
            $table->string('pdf_path')->nullable();
            $table->enum('type', ['magazine', 'paper'])->default('paper');
            $table->string('thumbnail')->nullable();
            $table->unsignedBigInteger('added_by')->nullable(); // Just an ID, not a foreign key
            $table->timestamps();
            $table->foreign('channel_id')->references('id')->on('channels')->onDelete('cascade');
            $table->foreign('news_language_id')->references('id')->on('news_languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('e_newspapers');
    }
};
