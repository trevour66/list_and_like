<?php

namespace App\Dashboard_Analytics;

use App\Models\user_mongodb_subprofile;
use App\Models\ig_business_account_posts;
use App\Models\ig_business_account_post_comments;
use App\Models\ig_profile_post;
use App\Models\ig_profiles;
use App\Models\user_list;

class Dashboard_Analytics
{
    private $user_id = null;
    private $user_email = null;
    private $IG_username = null;

    private $user_mongoDB = null;

    public $allIGBusinessPostProcessed = 0;
    public $allIGBusinessPostCommentsProcessed = 0;
    public $allIGProfilePostsFromCommentersProcessed = 0;
    public $allIGProfilePostsFromCommentersProcessed_skipped = 0;
    public $allIGProfilePostsFromCommentersProcessed_reactedTo = 0;
    public $allIGProfilesLinkedToIGBusinessAccount = 0;
    public $allUserLists = 0;

    public function __construct(
        $user_id,
        $user_email,
        $IG_username
    ) {
        $this->user_id = $user_id;
        $this->user_email = $user_email;
        $this->IG_username = $IG_username;

        $this->user_mongoDB = user_mongodb_subprofile::where([
            'email' => $this->user_email,
            'user_id' => $this->user_id,
        ])
            ->orderBy('_id')
            ->first() ?? false;
    }

    public function calculateData()
    {
        try {
            $this->allIGBusinessPostProcessed = $this->calculateIGBusinessPostProcessed();
            $this->allIGBusinessPostCommentsProcessed = $this->calculateIGBusinessPostCommentsProcessed();
            $this->allIGProfilePostsFromCommentersProcessed = $this->calculateIGProfilePostsFromCommentersProcessed();
            $this->allIGProfilePostsFromCommentersProcessed_skipped = $this->calculateIGProfilePostsFromCommentersProcessedSkipped();
            $this->allIGProfilePostsFromCommentersProcessed_reactedTo = $this->calculateIGProfilePostsFromCommentersProcessedReactedTo();
            $this->allIGProfilesLinkedToIGBusinessAccount = $this->calculateIGProfilesLinkedToBusinessAccount();
            $this->allUserLists = $this->calculateUserLists();

            // Logging
            // logger($this->user_mongoDB->user_id);
            // logger($this->allIGBusinessPostProcessed);
            // logger($this->allIGBusinessPostCommentsProcessed);
            // logger($this->allIGProfilePostsFromCommentersProcessed);
            // logger($this->allIGProfilePostsFromCommentersProcessed_skipped);
            // logger($this->allIGProfilePostsFromCommentersProcessed_reactedTo);
            // logger($this->allIGProfilesLinkedToIGBusinessAccount);
            // logger($this->allUserLists);
        } catch (\Exception $th) {
            logger('Dashboard_Analytics calculateData Error');
            logger(print_r($th->getMessage(), true));
        }
    }

    private function calculateIGBusinessPostProcessed()
    {
        try {
            return ig_business_account_posts::where('ig_business_account', '=', $this->IG_username)->count() ?? 0;
        } catch (\Exception $th) {
            logger('Error in calculateIGBusinessPostProcessed: ' . $th->getMessage());
            return null;
        }
    }

    private function calculateIGBusinessPostCommentsProcessed()
    {
        try {
            return ig_business_account_post_comments::where('ig_business_account', '=', $this->IG_username)->count() ?? 0;
        } catch (\Exception $th) {
            logger('Error in calculateIGBusinessPostCommentsProcessed: ' . $th->getMessage());
            return null;
        }
    }

    private function calculateIGProfilePostsFromCommentersProcessed()
    {
        try {
            $query = ig_profile_post::where('associated_ig_business_accounts', 'elemMatch', ['$in' => [$this->IG_username]]);

            return $query->count() ?? 0;
        } catch (\Exception $th) {
            logger('Error in calculateIGProfilePostsFromCommentersProcessed: ' . $th->getMessage());
            return null;
        }
    }

    private function calculateIGProfilePostsFromCommentersProcessedSkipped()
    {
        try {
            $query = ig_profile_post::where('associated_ig_business_accounts', 'elemMatch', ['$in' => [$this->IG_username]]);
            return $query->where('skipped_by', 'elemMatch', ['$in' => [$this->IG_username]])->count() ?? 0;
        } catch (\Exception $th) {
            logger('Error in calculateIGProfilePostsFromCommentersProcessedSkipped: ' . $th->getMessage());
            return null;
        }
    }

    private function calculateIGProfilePostsFromCommentersProcessedReactedTo()
    {
        try {
            $query = ig_profile_post::where('associated_ig_business_accounts', 'elemMatch', ['$in' => [$this->IG_username]]);
            return $query->where('reactedTo_by', 'elemMatch', ['$in' => [$this->IG_username]])->count() ?? 0;
        } catch (\Exception $th) {
            logger('Error in calculateIGProfilePostsFromCommentersProcessedReactedTo: ' . $th->getMessage());
            return null;
        }
    }

    private function calculateIGProfilesLinkedToBusinessAccount()
    {
        try {

            // logger($this->IG_username);
            $ig_profiles_query = ig_profiles::withWhereHas('ig_bis_account_posts_commented_on', function ($query) {
                $query->where('ig_business_account', $this->IG_username);
            });

            // logger($ig_profiles_query->get());

            return $ig_profiles_query->count() ?? 0;
        } catch (\Exception $th) {
            logger('Error in calculateIGProfilesLinkedToBusinessAccount: ' . $th->getMessage());
            return null;
        }
    }

    private function calculateUserLists()
    {
        try {
            return user_list::where('user_id', '=', $this->user_mongoDB->user_id)->count() ?? 0;
        } catch (\Exception $th) {
            logger('Error in calculateUserLists: ' . $th->getMessage());
            return null;
        }
    }
}
