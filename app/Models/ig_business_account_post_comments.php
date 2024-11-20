<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\HasOne;
use MongoDB\Laravel\Relations\HasMany;


class ig_business_account_post_comments extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'ig_business_account',
        'comment_id',
        'commenter_ig_username',
        'likesCount',
        'text',
        'timestamp',
        'parent_comment_id',

    ];

    public function commenter(): BelongsTo
    {
        return $this->belongsTo(ig_profiles::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(ig_business_account_posts::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ig_business_account_post_comments::class, 'parent_comment_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ig_business_account_post_comments::class, 'parent_comment_id');
    }
}
