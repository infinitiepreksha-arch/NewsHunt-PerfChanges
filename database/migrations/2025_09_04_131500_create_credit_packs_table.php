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
        Schema::create('credit_packs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('product_id')->unique();
            $table->integer('credits');
            $table->decimal('price', 10, 2);
            $table->string('tagline')->nullable();
            $table->unsignedTinyInteger('savings_percent')->default(0);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_best_value')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_packs');
    }
};
