<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Events\NewDataFetchRequest;


class ig_data_fetch_process extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'IDFP_status',
    ];

    // protected $dispatchesEvents = [
    //     'created' => NewDataFetchRequest::class,
    // ];

    public function ig_access_code(): BelongsTo
    {
        return $this->belongsTo(IGAccessCodes::class, "IDFP_ig_bussines_account");
    }
}
