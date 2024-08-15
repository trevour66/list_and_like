<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;


class user_engagements extends Model
{
    use HasFactory;

    protected $fillable = [
        "post_id",
        "engager_user_id",
        "engagement_type",
    ];
}
