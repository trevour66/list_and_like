<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;


class ig_business_account_post_commenter_to_be_scraped extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'ig_handle',
    ];
}
