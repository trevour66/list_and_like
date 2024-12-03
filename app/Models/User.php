<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = ['auth_token'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        'password' => 'hashed',
    ];

    /**
     * Get the IGAccessCodes associated with the user.
     */
    public function IGAccessCodes(): HasMany
    {
        return $this->hasMany(IGAccessCodes::class, 'user_id');
    }

    /**
     * Get the authRequest 
     */
    public function authRequest(): HasMany
    {
        return $this->hasMany(authorizationRequests::class, 'user_id');
    }

    public function getAuthTokenAttribute()
    {
        return $this->currentAccessToken() ? $this->currentAccessToken()->token : null;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $token = $this->getAuthTokenAttribute() ?? null;

        if ($token === null) {
            // $token = $this->createToken('auth-token')->plainTextToken;
        }

        $array['auth_token'] = $token;
        return $array;
    }
}
