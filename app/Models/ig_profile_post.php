<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;

class ig_profile_post extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'ig_profile_handle',
        'associated_ig_business_accounts',
        'post_id',
        'post_type',
        'caption',
        'hashtags',
        'url',
        'commentsCount',
        'displayUrl',
        'likesCount',
        'timestamp',

        'skipped_by',
        'reactedTo_by',
    ];

    protected $attributes = [
        'skipped_by' => [],
        'reactedTo_by' => [],
        'image_cdn' => ''
    ];

    public function owner_ig_profile(): BelongsTo
    {
        return $this->belongsTo(ig_profiles::class, 'ig_profiles_id');
    }
}
