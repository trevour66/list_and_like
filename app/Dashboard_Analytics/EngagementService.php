<?php

namespace App\Dashboard_Analytics;

use App\Models\ig_profile_post;
use App\Models\ig_profiles;
use App\Models\ig_business_account_post_comments;
use App\Models\IGAccessCodes;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Database\Eloquent\Builder;


class EngagementService
{
    private $IG_BusinessAccpunt_Username = '';
    private $profiles = null;
    private $profile_highestEngagement = null;
    private $profile_lowestEngagement = null;

    private $profiles_highestEngagement = [];
    private $profiles_lowestEngagement = [];

    public function __construct($businessAccountId)
    {
        $this->IG_BusinessAccpunt_Username = $businessAccountId;
    }

    private function parseHighestAndLowest($profile)
    {
        // Initialize highest and lowest engagement profiles if they haven't been set
        if ($this->profile_highestEngagement === null && $this->profile_lowestEngagement === null) {
            $this->profile_highestEngagement = $profile;
            $this->profile_lowestEngagement = $profile;
            $this->profiles_highestEngagement = [$profile];
            $this->profiles_lowestEngagement = [$profile];
            return;
        }

        // Check if the current profile has a higher engagement than the current highest
        if ($this->profile_highestEngagement->postCount < $profile->postCount) {
            $this->profile_highestEngagement = $profile;
            $this->profiles_highestEngagement = [$profile];
        } elseif ($this->profile_highestEngagement->postCount === $profile->postCount) {
            $this->profiles_highestEngagement[] = $profile;
        }

        // Check if the current profile has a lower engagement than the current lowest
        if ($this->profile_lowestEngagement->postCount > $profile->postCount) {
            $this->profile_lowestEngagement = $profile;
            $this->profiles_lowestEngagement = [$profile];
        } elseif ($this->profile_lowestEngagement->postCount === $profile->postCount) {
            $this->profiles_lowestEngagement[] = $profile;
        }
    }

    public function prepareEngagementProfile()
    {
        $businessAccountId = $this->IG_BusinessAccpunt_Username;

        $profiles = ig_profiles::withWhereHas('ig_bis_account_posts_commented_on', function ($query)  use ($businessAccountId) {
            $query->where('ig_business_account', 'like', "%$businessAccountId%");
        })
            ->select(['ig_handle', 'ig_bis_account_posts_commented_on']);


        $this->profiles = $profiles->get();

        // logger($profiles->get());
        // logger($profiles->count());

        foreach ($this->profiles as $profile) {
            $profile->postCount = $profile->ig_bis_account_posts_commented_on->count();

            $this->parseHighestAndLowest($profile);
        }
    }

    public function getHighestEngaged()
    {
        return $this->profile_highestEngagement;
    }

    public function getHighestEngagers()
    {
        return $this->profiles_highestEngagement;
    }

    public function getLowestEngaged()
    {
        return $this->profile_lowestEngagement;
    }

    public function getLowestEngagers()
    {
        return $this->profiles_lowestEngagement;
    }

    public function getAllData()
    {
        return $this->profiles;
    }
}
