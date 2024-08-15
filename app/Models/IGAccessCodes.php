<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use MongoDB\Laravel\Relations\BelongsTo;

use App\Events\NewConnectedIGBusinessAccount;


class IGAccessCodes extends Model
{
    use HasFactory;

    protected $table = "ig_access_codes";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'IG_APP_SCOPED_ID',
        'IG_USERNAME',
        'short_lived_access_token',
        'long_lived_access_token',
        'long_lived_expires_in',
        'permissions'
    ];

    protected $dispatchesEvents = [
        'created' => NewConnectedIGBusinessAccount::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
