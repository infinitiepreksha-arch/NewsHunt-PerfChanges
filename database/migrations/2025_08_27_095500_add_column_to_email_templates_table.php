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
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('subject')->after('status')->nullable();
            $table->string('logo')->after('subject')->nullable();
            $table->string('image')->after('logo')->nullable();
            $table->string('type', 100)->after('image')->nullable();

            // ✅ new columns
            $table->string('closing')->after('type')->nullable();
            $table->text('signature')->after('closing')->nullable();
            $table->text('footer_text')->after('signature')->nullable();

            $table->integer('post_count')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            if (Schema::hasColumn('email_templates', 'subject')) {
                $table->dropColumn('subject');
            }
            if (Schema::hasColumn('email_templates', 'logo')) {
                $table->dropColumn('logo');
            }
            if (Schema::hasColumn('email_templates', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('email_templates', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('email_templates', 'closing')) {
                $table->dropColumn('closing');
            }
            if (Schema::hasColumn('email_templates', 'signature')) {
                $table->dropColumn('signature');
            }
            if (Schema::hasColumn('email_templates', 'footer_text')) {
                $table->dropColumn('footer_text');
            }
            if (Schema::hasColumn('email_templates', 'post_count')) {
                $table->integer('post_count')->default(5)->nullable(false)->change();
            }
        });
    }

};
