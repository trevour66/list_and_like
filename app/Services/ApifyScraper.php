<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\ig_business_account_post_commenter_to_be_scraped;
use App\Models\ig_profiles;
use App\Models\ig_profile_post;
use App\Models\user_mongodb_subprofile;

class ApifyScraper
{
    private $base_uri = 'https://api.apify.com/v2/';
    private $headers = [];

    public function __construct()
    {
        $this->headers['Authorization'] = 'Bearer ' . config('apify.APIFY_TOKEN');
    }

    public function scrape()
    {
        $url = $this->base_uri . 'acts/apify~instagram-profile-scraper/run-sync-get-dataset-items';

        // $usernames = ig_business_account_post_commenter_to_be_scraped::take(1)->pluck('ig_handle')->toArray();
        $usernames = ig_business_account_post_commenter_to_be_scraped::take(10)->pluck('ig_handle')->toArray();

        logger(print_r($usernames, true));

        // return;

        $response = Http::withHeaders($this->headers)
            ->withQueryParameters(
                [
                    'token' => config('apify.APIFY_TOKEN')
                ]
            )
            ->timeout(3000)
            ->post($url, [
                "usernames" => $usernames
            ]);

        $data = $response->json() ?? [];

        // logger(print_r($data, true));

        foreach ($data as $item) {
            $profileData = [
                'ig_handle' => $item['username'] ?? '',
                'account_link' => $item['url'] ?? '',
                'profile_pic' => $item['profilePicUrl'] ?? '',
                'bio' => $item['biography'] ?? '',
                'post_count' => $item['postsCount'] ?? 0,
                'followers' => $item['followersCount'] ?? 0,
                'following' => $item['followsCount'] ?? 0,
                'verified' => $item['verified'] ?? false,
            ];

            $this->updateIGProfile($profileData, ($item['latestPosts'] ?? []));
        }

        ig_business_account_post_commenter_to_be_scraped::whereIn('ig_handle', $usernames)->delete();

        logger(print_r("done", true));
    }

    protected function updateIGProfile($data, $latest_posts)
    {
        if (!($data["ig_handle"] ?? false)) {
            return;
        }

        $createdOrUpdatedIGProfile = ig_profiles::updateOrCreate(
            ['ig_handle' => $data["ig_handle"]],
            $data
        );


        foreach ($latest_posts as $post) {
            if (!($post['id'] ?? false)) {
                continue;
            }
            $postdataArray = [
                'ig_profile_handle' => $data["ig_handle"],
                'post_id' => $post['id'],
                'post_type' => $post['type'] ?? '',
                'caption' => $post['caption'] ?? '',
                'hashtags' => $post['hashtags'] ?? [],
                'url' => $post['url'] ?? '',
                'commentsCount' => $post['commentsCount'] ?? 0,
                'displayUrl' => $post['displayUrl'] ?? '',
                'likesCount' => $post['likesCount'] ?? 0,
                'timestamp' => $post['timestamp'] ?? '',
            ];

            $createdOrUpdatedIGPost = ig_profile_post::updateOrCreate(
                ['post_id' => $post['id']],
                $postdataArray
            );

            $user_mongodb_subprofile_user_ids = $createdOrUpdatedIGProfile->user_mongodb_subprofile_user_ids ?? [];
            $associated_ig_bussiness_acc  = [];

            // logger(print_r($user_mongodb_subprofile_user_ids, true));

            if (
                $user_mongodb_subprofile_user_ids &&
                (count($user_mongodb_subprofile_user_ids ?? []) > 0)
            ) {
                for ($i = 0; $i < count($user_mongodb_subprofile_user_ids); $i++) {
                    $cur = $user_mongodb_subprofile_user_ids[$i];
                    $user_mongodb_subprofile_user = user_mongodb_subprofile::where("user_id", "=", $cur)->first() ?? false;

                    if ($user_mongodb_subprofile_user) {
                        $associated_ig_bussiness_acc = array_merge($associated_ig_bussiness_acc, ($user_mongodb_subprofile_user->IG_bussiness_accounts ?? []));
                    }
                }
            }

            // logger(print_r($associated_ig_bussiness_acc, true));

            $createdOrUpdatedIGProfile->ig_posts()->save($createdOrUpdatedIGPost);

            // $createdOrUpdatedIGPost->owner_ig_profile->attach()

            if (count($associated_ig_bussiness_acc ?? []) > 0) {
                ig_profile_post::where('post_id', $post['id'])
                    ->push(
                        'associated_ig_business_accounts',
                        $associated_ig_bussiness_acc,
                        unique: true
                    );
            }
        }
    }
}
