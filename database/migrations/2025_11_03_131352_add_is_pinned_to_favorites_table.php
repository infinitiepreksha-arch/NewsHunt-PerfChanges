<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Remove rows with invalid post_id
        DB::table('favorites')
            ->whereNotIn('post_id', function ($query) {
                $query->select('id')->from('posts');
            })
            ->delete();

        // 2️⃣ Now modify table safely
        Schema::table('favorites', function (Blueprint $table) {
            if (!Schema::hasColumn('favorites', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('post_id');
            }

            $table->foreign('post_id')
                ->references('id')
                ->on('posts')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropColumn('is_pinned');
        });
    }
};
