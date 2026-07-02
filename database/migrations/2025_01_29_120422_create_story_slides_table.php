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
        Schema::create('story_slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained('stories')->onDelete('cascade'); // Foreign key to stories table
            $table->string('image'); // Image column for the slide
            $table->string('title'); // Title column for the slide
            $table->text('description'); // Description column for the slide
            $table->integer('order')->default(0);
            $table->text('animation_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('story_slides', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
