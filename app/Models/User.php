<?php
namespace App\Models;

use DevDojo\LaravelReactions\Traits\Reacts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, HasPermissions, Reacts;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'type',
        'fcm_id',
        'firebase_id',
        'profile',
        'address',
        'notification',
        'country_code',
        'is_blocked',
        'block_type',
        'block_reason',
        'status',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope('withPermissionsAndRoles', function ($builder) {
            $builder->with(['permissions', 'roles.permissions']);
        });
    }

    public function getProfileAttribute($image)
    {
        if (! empty($image) && ! filter_var($image, FILTER_VALIDATE_URL)) {
            return url(Storage::url($image));
        }
        return $image;
    }

    /* Define relationships */
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'user_id', 'blocked_user_id');
    }

    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    /* user subscriber of news channels*/
    public function subscriptions()
    {
        return $this->belongsToMany(Channel::class, 'channel_subscribers', 'user_id', 'channel_id');
    }

    public function followedLanguages()
    {

        return $this->belongsToMany(NewsLanguage::class, 'news_language_subscribers', 'user_id', 'news_language_id');

    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function credits()
    {
        return $this->hasOne(UserCredits::class);
    }

    public function hasPermission($permission)
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }

    // public function isAdmin()
    // {
    //     return $this->hasRole('Admin') || $this->permissions->isNotEmpty() || $this->roles->whereNotIn('name', ['user', 'User'])->isNotEmpty();
    // }

    // public function sendPasswordResetNotification($token)
    // {
    //     $url = url(route('password.reset', [
    //         'token' => $token,
    //         'email' => $this->email,
    //     ], false));

    //     Mail::to($this->email)->send(new ResetPasswordMail($url));
    // }
}
