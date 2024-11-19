<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class user_mongodb_subprofile extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'email',
        'IG_bussiness_accounts'
    ];

    public function ig_profiles_added(): BelongsToMany
    {
        return $this->belongsToMany(ig_profiles::class);
    }

    public function ig_business_account_posts_added(): HasMany
    {
        return $this->hasMany(ig_business_account_posts::class);
    }
}
