<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {

        if (!Schema::hasTable('countries')) {
            Schema::create('countries', static function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('iso_code')->nullable();
                $table->string('slug')->nullable()->unique();
                $table->char('iso3', 3)->nullable();
                $table->char('numeric_code', 3)->nullable();
                $table->char('iso2', 2)->nullable();
                $table->string('phonecode', 255)->nullable();
                $table->string('capital', 255)->nullable();
                $table->string('currency', 255)->nullable();
                $table->string('currency_name', 255)->nullable();
                $table->string('currency_symbol', 255)->nullable();
                $table->string('tld', 255)->nullable();
                $table->string('native', 255)->nullable();
                $table->string('region', 255)->nullable();
                $table->integer('region_id')->nullable();
                $table->string('subregion', 255)->nullable();
                $table->integer('subregion_id')->nullable();
                $table->string('nationality', 255)->nullable();
                $table->text('timezones')->nullable();
                $table->text('translations')->nullable();
                $table->decimal('latitude')->nullable();
                $table->decimal('longitude')->nullable();
                $table->string('emoji', 191)->nullable();
                $table->string('emojiU', 191)->nullable();
                $table->boolean('flag')->nullable();
                $table->string('wikiDataId', 255)->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('states')) {
            Schema::create('states', static function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->foreignId('country_id')->references('id')->on('countries')->onDelete('cascade');
                $table->char('state_code', 2);
                $table->string('fips_code', 255)->nullable();
                $table->string('iso2', 255)->nullable();
                $table->string('type', 191)->nullable();
                $table->decimal('latitude')->nullable();
                $table->decimal('longitude')->nullable();
                $table->boolean('flag')->nullable();
                $table->string('wikiDataId', 255)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cities')) {
            Schema::create('cities', static function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->foreignId('state_id')->references('id')->on('states')->onDelete('cascade');
                $table->string('state_code', 255);
                $table->foreignId('country_id')->references('id')->on('countries')->onDelete('cascade');
                $table->char('country_code', 2);
                $table->decimal('latitude')->nullable();
                $table->decimal('longitude')->nullable();
                $table->boolean('flag')->nullable();
                $table->string('wikiDataId', 255)->nullable();
                $table->timestamps();
            });
        }



        if (!Schema::hasTable('social_logins')) {
            Schema::create('social_logins', static function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('firebase_id', 512);
                $table->enum('type', ['google', 'email', 'phone']);
                $table->timestamps();
                $table->unique(['user_id', 'type']);
            });
        }

        Schema::table('languages', static function (Blueprint $table) {
            if (!Schema::hasColumn('languages', 'slug')) {
                $table->string('slug', 512)->after('name');
            }
            if (!Schema::hasColumn('languages', 'web_file')) {
                $table->string('web_file')->after('panel_file');
            }
        });
        Schema::table('users', static function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'country_code')) {
                $table->string('country_code')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public
    function down(): void {
        Schema::dropIfExists('social_logins');



        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn('country_code');
        });


        Schema::table('languages', static function (Blueprint $table) {
            $table->dropColumn('web_file');
        });
    }
};
