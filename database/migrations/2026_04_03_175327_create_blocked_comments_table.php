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
        Schema::create('blocked_comments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('blocker_user_id');
            $table->unsignedBigInteger('comment_id');
            $table->unsignedBigInteger('owner_user_id');

            $table->boolean('status')->default(1);
            $table->string('block_reason')->nullable();
            $table->timestamps();

            $table->unique(['blocker_user_id', 'comment_id']);

            // ✅ Foreign Keys with Cascade Delete
            $table->foreign('comment_id')
                ->references('id')
                ->on('comments')
                ->onDelete('cascade');

            $table->foreign('blocker_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('owner_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_comments');
    }
};
