<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('languages', function (Blueprint $table) {

            $table->dropColumn(['web_file', 'app_file', 'panel_file', 'iso_code']);
            $table->json('admin_panel_files')->nullable()->after('name_in_english');
            $table->json('web_files')->nullable()->after('admin_panel_files');
        });
    }

    public function down(): void
    {
        Schema::table('languages', function (Blueprint $table) {
            $table->string('web_file')->nullable();
            $table->string('app_file')->nullable();
            $table->string('panel_file')->nullable();
            $table->string('iso_code')->nullable();
            $table->dropColumn(['admin_panel_files', 'web_files']);
        });
    }
};
