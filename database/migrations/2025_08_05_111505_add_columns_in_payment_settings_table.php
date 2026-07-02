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
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->string('apple_shared_secret')->nullable();
            $table->string('apple_issuer_id')->nullable();
            $table->string('apple_key_id')->nullable();
            $table->string('apple_bundle_id')->nullable();
            $table->string('apple_api_key_path')->nullable();
            $table->enum('apple_environment', ['Sandbox', 'Production'])->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->dropColumn([
                'apple_shared_secret',
                'apple_issuer_id',
                'apple_key_id',
                'apple_bundle_id',
                'apple_api_key_path',
                'apple_environment',
            ]);
        });
    }
};
