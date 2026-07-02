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
        Schema::table('report_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('report_type_id')->nullable()->after('comment_id');
            $table->boolean('is_other')->default(false)->after('report_type_id');
            $table->string('other_type')->nullable()->after('is_other');
            $table->enum('status', ['pending', 'ignored', 'removed'])->default('pending')->after('other_type');
            $table->unsignedBigInteger('action_taken_by')->nullable()->after('status');
            $table->timestamp('action_at')->nullable()->after('action_taken_by');
            $table->foreign('report_type_id')->references('id')->on('report_types')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    
    public function down(): void
    {
        Schema::table('report_comments', function (Blueprint $table) {
            $table->dropForeign(['report_type_id']);
            $table->dropColumn(['report_type_id', 'is_other', 'other_type', 'status', 'action_taken_by', 'action_at']);
        });
    }
};
