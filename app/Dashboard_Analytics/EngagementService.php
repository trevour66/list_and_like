<?php

namespace App\Dashboard_Analytics;


use App\Dashboard_Analytics\EngagementAnalyzerService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Cursor;
use Illuminate\Http\Request;


use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;


class EngagementService extends EngagementAnalyzerService
{
    private $IG_BusinessAccount_Username = '';
    private $profiles = null;

    public function __construct($businessAccountId)
    {
        $this->IG_BusinessAccount_Username = $businessAccountId;
    }

    public function fetch_data($data, Request $request)
    {
        $items = json_decode($data, true) ?? [];

        // logger($items);
        // logger(count($items));

        $total = count($items);
        $per_page = 20;
        $current_page = $request->input("page") ?? 1;

        // logger($current_page);

        $starting_point = ($current_page * $per_page) - $per_page;

        $array = array_slice($items, $starting_point, $per_page, true);

        $paginatedItems = new LengthAwarePaginator($array, $total, $per_page, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

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

    public function getOtherEngagers__withPagination(Request $request)
    {
        $profiles_other_Engagement = Cache::store('redis')->get($this->IG_BusinessAccount_Username . 'other_profiles', collect());
        // logger($profiles_other_Engagement);

        return $this->fetch_data($profiles_other_Engagement, $request);
    }
}
