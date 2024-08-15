<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsToMany;
use MongoDB\Laravel\Relations\HasMany;

class ig_profiles extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ig_handle',
        'account_link',
        'profile_pic',
        'bio',
        'post_count',
        'followers',
        'following',
        'verified',
        'last_fetch',
    ];

    public function users_ids(): BelongsToMany
    {
        return $this->belongsToMany(user_mongodb_subprofile::class);
    }

    public function ig_posts(): HasMany
    {
        return $this->hasMany(ig_profile_post::class);
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(user_list::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ig_business_account_post_comments::class);
    }

    public function ig_bis_account_posts_commented_on(): BelongsToMany
    {
        return $this->belongsToMany(ig_business_account_posts::class);
    }
}
