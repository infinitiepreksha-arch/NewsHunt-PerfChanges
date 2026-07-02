<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            DB::statement("ALTER TABLE transaction MODIFY payment_gateway ENUM('razorpay', 'stripe', 'applepay') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            DB::statement("ALTER TABLE transaction MODIFY payment_gateway ENUM('razorpay', 'stripe') NOT NULL");
        });
    }
};
