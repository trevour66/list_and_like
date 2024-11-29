<?php

namespace App\Dashboard_Analytics;

use App\Models\ig_profile_post;
use App\Models\ig_profiles;
use App\Dashboard_Analytics\EngagementAnalyzerService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Cursor;


class EngagementService extends EngagementAnalyzerService
{
    private $IG_BusinessAccount_Username = '';
    private $profiles = null;

    public function __construct($businessAccountId)
    {
        $this->IG_BusinessAccount_Username = $businessAccountId;
    }

    public function fetch_data($data)
    {
        $items = json_decode($data, true) ?? [];

        // logger($items);

        $perPage = 10;

        $paginatedItems = new CursorPaginator(
            $items, // Items for the current page
            $perPage,
            null
            // [
            //     'path' => $request->url(), // Base URL
            // ]
        );

        // logger($paginatedItems);

        return $paginatedItems;
    }

    public function getHighestEngagers()
    {
        $profiles_highestEngagement = Cache::store('redis')->get($this->IG_BusinessAccount_Username . 'highest_profile', collect());
        // logger($profiles_highestEngagement);

        return $profiles_highestEngagement;
    }

    public function getLowestEngagers()
    {
        $profiles_lowestEngagement = Cache::store('redis')->get($this->IG_BusinessAccount_Username . 'lowest_profile', collect());
        // logger($profiles_lowestEngagement);
        return $profiles_lowestEngagement;
    }

    public function getTopFiveEngagers()
    {
        $profiles_top_five_Engagement = Cache::store('redis')->get($this->IG_BusinessAccount_Username . 'top_five_profiles', collect());
        // logger($profiles_top_five_Engagement);
        // logger("profiles_top_five_Engagement");

        return $profiles_top_five_Engagement;
    }

    public function getOtherEngagers()
    {
        $profiles_other_Engagement = Cache::store('redis')->get($this->IG_BusinessAccount_Username . 'other_profiles', collect());
        // logger($profiles_other_Engagement);

        return $profiles_other_Engagement;
    }

    public function getOtherEngagers__withPagination()
    {
        $profiles_other_Engagement = Cache::store('redis')->get($this->IG_BusinessAccount_Username . 'other_profiles', collect());
        // logger($profiles_other_Engagement);

        return $this->fetch_data($profiles_other_Engagement);
    }
}
