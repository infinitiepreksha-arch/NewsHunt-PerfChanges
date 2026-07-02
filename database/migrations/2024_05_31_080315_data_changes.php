<?php

use App\Models\SocialLogin;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {

        /* 2. User Changes */
        $socialLogin = [];
        $userRole = (new Spatie\Permission\Models\Role)->where('name', 'User')->count();
        if ($userRole > 0) {
            foreach (User::role('User')->whereNotNull('firebase_id')->get() as $user) {
                $socialLogin[] = [
                    'firebase_id' => $user->firebase_id,
                    'type'        => $user->type,
                    'user_id'     => $user->id
                ];
            }
            if (empty($socialLogin)) {
                SocialLogin::upsert($socialLogin, ['user_id', 'type'], ['firebase_id']);
            }
        }

        Schema::table('users', static function (Blueprint $table) {
            $table->string('firebase_id')->comment('remove in next update')->nullable()->change();
            $table->string('type')->comment('remove in next update')->nullable()->change();
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {

        Schema::table('users', static function (Blueprint $table) {
            $table->string('type')->comment('email/google/mobile')->nullable(false)->change();
            $table->string('firebase_id')->nullable(false)->change();
        });
    }
};
