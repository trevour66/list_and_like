<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\BelongsToMany;


use MongoDB\Laravel\Relations\HasMany;


class ig_business_account_posts extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'ig_business_account',
        'id',
        'caption',
        'commentsCount',
        'likesCount',
        'displayUrl',
        'timestamp',
        'url',
        'media_product_type',
    ];


    public function owner_user_mongodb_profile(): BelongsTo
    {
        return $this->belongsTo(user_mongodb_subprofile::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ig_business_account_post_comments::class);
    }

    public function commenters(): BelongsToMany
    {
        return $this->belongsToMany(ig_profiles::class);
    }
}
