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
        Schema::create('features', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('plan_id');
        $table->boolean('is_ads_free')->default(false);
        $table->integer('number_of_articles')->default(0);
        $table->integer('number_of_stories')->default(0);
        $table->timestamps();

        $table->foreign('plan_id')
            ->references('id')
            ->on('plans')
            ->onDelete('cascade');
    });

}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
