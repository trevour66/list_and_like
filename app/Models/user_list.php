<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\BelongsToMany;
use MongoDB\Laravel\Relations\HasMany;

class user_list extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'list_name',
        'list_description',
        'ig_business_account',
        'user_id',
        'list_webhook_id'
    ];

    public function user_mongodb_subprofile(): BelongsTo
    {
        return $this->belongsTo(user_mongodb_subprofile::class, 'user_mongodb_subprofile_user_id');
    }

    // public function ig_profiles(): HasMany
    // {
    //     return $this->hasMany(ig_profiles::class);
    // }


    public function ig_profiles(): BelongsToMany
    {
        return $this->belongsToMany(ig_profiles::class);
    }
}
