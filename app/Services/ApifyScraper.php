<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\ig_business_account_post_commenter_to_be_scraped;
use App\Models\ig_profiles;
use App\Models\ig_profile_post;
use App\Models\ig_data_fetch_process;
use App\Notifications\SuccessfulDataFetch;
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

        $this->usernames_resulting_ig_business_acc_array = ig_business_account_post_commenter_to_be_scraped::select('resulting_ig_data_fetch_processes', 'resulting_ig_business_accounts', 'ig_handle')->latest()->limit(10)->get();

        $usernames =  [];

        foreach ($this->usernames_resulting_ig_business_acc_array as $key => $value) {
            array_push($usernames, $value->ig_handle);
        }

        // logger(print_r($this->usernames_resulting_ig_business_acc_array, true));
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

        $this->sendSuccessNotification();

        logger(print_r("API scraping done", true));
    }

    public function scrape_single($ig_handle, $associated_ig_business_accounts)
    {
        $url = $this->base_uri . 'acts/apify~instagram-profile-scraper/run-sync-get-dataset-items';

        $response = Http::withHeaders($this->headers)
            ->withQueryParameters(
                [
                    'token' => config('apify.APIFY_TOKEN')
                ]
            )
            ->timeout(3000)
            ->post($url, [
                "usernames" => [$ig_handle]
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

            $latest_posts = $item['latestPosts'] ?? [];

            if (!($profileData["ig_handle"] ?? false)) {
                continue;
            }

            $createdOrUpdatedIGProfile = ig_profiles::updateOrCreate(
                ['ig_handle' => $profileData["ig_handle"]],
                $profileData
            );


            foreach ($latest_posts as $post) {
                if (!($post['id'] ?? false)) {
                    continue;
                }
                $postdataArray = [
                    'ig_profile_handle' => $profileData["ig_handle"],
                    'post_id' => $post['id'],
                    'post_type' => $post['type'] ?? '',
                    'caption' => $post['caption'] ?? '',
                    'hashtags' => $post['hashtags'] ?? [],
                    'url' => $post['url'] ?? '',
                    'commentsCount' => $post['commentsCount'] ?? 0,
                    'displayUrl' => $post['displayUrl'] ?? '',
                    'likesCount' => $post['likesCount'] ?? 0,
                    'timestamp' => $post['timestamp'] ?? '',
                    'error_current_count' => 0
                ];

                $createdOrUpdatedIGPost = ig_profile_post::updateOrCreate(
                    ['post_id' => $post['id']],
                    $postdataArray
                );

                $createdOrUpdatedIGProfile->ig_posts()->save($createdOrUpdatedIGPost);


                ig_profile_post::where('post_id', $post['id'])
                    ->push(
                        'associated_ig_business_accounts',
                        $associated_ig_business_accounts,
                        unique: true
                    );
            }
        }


        logger(print_r("API scraping [scrape_single] done", true));
        return true;
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
                'error_current_count' => 0
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

    private function separateString($input)
    {
        $parts = explode("%__%", $input);

        if (count($parts) === 2) {
            $IG_Business_account = $parts[0]; // First part before the separator
            $fetch_proccess_id = $parts[1]; // Second part after the separator

            return [$IG_Business_account, $fetch_proccess_id];
        } else {
            return [null, null];
        }
    }

    private function sendSuccessNotification()
    {
        foreach ($this->usernames_resulting_ig_business_acc_array as $key => $value) {
            // logger(print_r($value->resulting_ig_data_fetch_processes, true));
            $resulting_ig_data_fetch_processes =  $value->resulting_ig_data_fetch_processes ?? [];


            foreach ($resulting_ig_data_fetch_processes as $key => $value) {
                // logger(print_r($value, true));

                $resulting_ig_data_fetch_process = $value;

                $result = ig_business_account_post_commenter_to_be_scraped::where('resulting_ig_data_fetch_processes', 'elemMatch', ['$in' => [$resulting_ig_data_fetch_process]])
                    ->get() ?? [];

                // logger(print_r($result, true));
                // logger(print_r(count($result), true));
                // logger(print_r("result", true));

                if (count($result) !== 0) {
                    continue;
                }


                list($IG_Business_account, $fetch_proccess_id) = $this->separateString($resulting_ig_data_fetch_process);

                $current_ig_data_fetch_process = ig_data_fetch_process::find($fetch_proccess_id);

                // logger(print_r("result_after 2", true));
                // logger(print_r($fetch_proccess_id, true));
                // logger(print_r($IG_Business_account, true));

                $current_ig_data_fetch_process->IDFP_status = 'finished_success';
                $current_ig_data_fetch_process->save();

                $IGAccountUnder = $current_ig_data_fetch_process->ig_access_code;
                $user = $current_ig_data_fetch_process->ig_access_code->user;

                try {

                    $user->notify(new SuccessfulDataFetch($IGAccountUnder));
                } catch (\Throwable $th) {
                    logger(print_r("sendSuccessNotification Notify Error:", true));
                    logger(print_r($th->getMessage(), true));
                }
            }
        }
    }
}
