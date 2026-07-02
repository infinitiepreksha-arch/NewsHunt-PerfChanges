<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            DB::statement("ALTER TABLE posts MODIFY COLUMN type ENUM('post', 'video', 'youtube', 'audio') AFTER slug");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            DB::statement("ALTER TABLE posts MODIFY COLUMN type ENUM('post', 'video', 'youtube') AFTER slug");
        });
    }
};
