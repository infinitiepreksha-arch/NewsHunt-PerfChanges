<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {



        Schema::create('languages', static function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('name_in_english', 32);
            $table->string('app_file');
            $table->string('panel_file');
            $table->boolean('rtl');
            $table->string('image', 512)->nullable();
            $table->timestamps();
        });

        Schema::create('settings', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'file'])->default('string');
            $table->timestamps();
        });



        Schema::create('notifications', static function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('message');
            $table->text('image');
            $table->enum('send_to', ['all', 'selected']);
            $table->string('user_id', 512)->nullable();
            $table->timestamps();
        });




        Schema::table('roles', static function (Blueprint $table) {
            $table->boolean('custom_role')->after('guard_name')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('languages');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('notification');
        Schema::dropIfExists('roles');
    }
};
