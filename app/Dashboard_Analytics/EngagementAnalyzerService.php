<?php

namespace App\Dashboard_Analytics;

use App\Models\ig_profiles;
use App\Models\IGAccessCodes;
use Illuminate\Support\Facades\Cache;


class EngagementAnalyzerService
{
    private $profiles = null;

    public function __construct() {}

    public function prepareEngagements()
    {
        $access_codes = IGAccessCodes::get();

        foreach ($access_codes as $access_code) {
            $businessAccountId = $access_code->IG_USERNAME ?? '';

            if ($businessAccountId == '') continue;

            // logger($businessAccountId);

            $this->analyze_business_account($businessAccountId);

            // logger(Cache::store('redis')->get($businessAccountId . 'highest_profile'));
            // logger(Cache::store('redis')->get($businessAccountId . 'lowest_profile'));
            // logger(Cache::store('redis')->get($businessAccountId . 'top_five_profiles'));
            // logger(Cache::store('redis')->get($businessAccountId . 'other_profiles'));
        }
    }

    protected function analyze_business_account($businessAccountId)
    {
        try {

            $profiles = ig_profiles::withWhereHas('ig_bis_account_posts_commented_on', function ($query)  use ($businessAccountId) {
                $query->where('ig_business_account', 'like', "%$businessAccountId%");
            })
                ->select([
                    'ig_handle',
                    'account_link',
                    'profile_pic',
                    'bio',
                    'post_count',
                    'followers',
                    'following',
                    'verified',
                    'last_fetch',
                    'ig_bis_account_posts_commented_on'
                ]);


            $this->profiles = $profiles->get() ?? collect([]);

            // logger($profiles->get());
            // logger($profiles->count());            

            foreach ($this->profiles as $profile) {
                $profile->postCount = $profile->ig_bis_account_posts_commented_on->count();
                unset($profile->ig_bis_account_posts_commented_on);
            }

            // logger($this->profiles); // 10 Minutes

            $this->save_highest_toCache($businessAccountId);
            $this->save_lowest_toCache($businessAccountId);
            $this->save_topfive_toCache($businessAccountId);
            $this->save_others_toCache($businessAccountId);

            $this->profiles = collect([]);
        } catch (\Throwable $th) {
            //throw $th;
            logger("Error analyzing: " . $businessAccountId);
            logger("Error: " . $th->getMessage());
        }
    }

    public function save_highest_toCache($businessAccountId)
    {
        // Find profile(s) with the highest postCount
        $highest = $this->profiles->filter(function ($profile) {
            return $profile['postCount'] === $this->profiles->max('postCount');
        })->values();

        // Save to cache
        Cache::store('redis')->put($businessAccountId . 'highest_profile', $highest, now()->addHours(4));
    }

    public function save_lowest_toCache($businessAccountId)
    {
        // Find profile(s) with the lowest postCount
        $lowest = $this->profiles->filter(function ($profile) {
            return $profile['postCount'] === $this->profiles->min('postCount');
        })->values();

        // Save to cache
        Cache::store('redis')->put($businessAccountId .  'lowest_profile', $lowest, now()->addHours(4));
    }

    public function save_topfive_toCache($businessAccountId)
    {
        // Get top 5 profiles with the highest postCount
        $topFive = $this->profiles->sortByDesc('postCount')->take(5)->values();

        // Save to cache
        Cache::store('redis')->put($businessAccountId . 'top_five_profiles', $topFive, now()->addHours(4));
    }

    public function save_others_toCache($businessAccountId)
    {
        // Get profiles excluding the top 5
        $topFiveIds = $this->profiles->sortByDesc('postCount')->take(5)->pluck('_id');

        $others = $this->profiles->reject(function ($profile) use ($topFiveIds) {
            return $topFiveIds->contains($profile['_id']) || $profile->postCount == 1;
        })->sortByDesc('postCount')->values();

        // Save to cache
        Cache::store('redis')->put($businessAccountId . 'other_profiles', $others, now()->addHours(4));
    }
}
