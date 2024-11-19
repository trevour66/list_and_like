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
    private $usernames_resulting_ig_business_acc_array = [];

    public function __construct()
    {
        $this->headers['Authorization'] = 'Bearer ' . config('apify.APIFY_TOKEN');
    }

    public function scrape()
    {
        $url = $this->base_uri . 'acts/apify~instagram-profile-scraper/run-sync-get-dataset-items';

        // $usernames = ig_business_account_post_commenter_to_be_scraped::take(1)->pluck('ig_handle')->toArray();
        $this->usernames_resulting_ig_business_acc_array = ig_business_account_post_commenter_to_be_scraped::take(10)->pluck('resulting_ig_business_accounts', 'ig_handle')->toArray();

        // logger(print_r($this->usernames_resulting_ig_business_acc_array, true));

        $usernames = array_keys($this->usernames_resulting_ig_business_acc_array);
        // logger(print_r($usernames, true));

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

        logger(print_r("API scraping done", true));
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

            $createdOrUpdatedIGProfile->ig_posts()->save($createdOrUpdatedIGPost);


            if (count($this->usernames_resulting_ig_business_acc_array[$data["ig_handle"]] ?? []) > 0) {
                ig_profile_post::where('post_id', $post['id'])
                    ->push(
                        'associated_ig_business_accounts',
                        $this->usernames_resulting_ig_business_acc_array[$data["ig_handle"]],
                        unique: true
                    );
            }
        }
    }
}
