<?php

namespace App\Services;

use App\Models\ig_business_account_posts;
use App\Models\ig_business_account_post_comments;
use App\Models\ig_profiles;
use App\Models\User;
use App\Models\IGAccessCodes;
use App\Models\user_mongodb_subprofile;
use App\Models\ig_business_account_post_commenter_to_be_scraped;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Number;

class IGMedia
{

    private ?IGAccessCodes $IG_access_codes = null;
    private ?User $user = null;
    private $ig_data_fetch_process_id = 0;


    private $IG_URL = 'graph.instagram.com';
    private $default_timeFrame = '3 months';
    private $allPosts = [];
    private $commenters_tracker = [];
    private $repliesIDStack = [];

    private $commentersProfileAddedForScrapping = false;

    public function __construct(
        IGAccessCodes $IG_access_codes,
        User $user,
        $ig_data_fetch_process_id,
    ) {
        $this->IG_access_codes = $IG_access_codes;
        $this->user = $user;
        $this->ig_data_fetch_process_id = $ig_data_fetch_process_id;
    }

    public function pullRecentUserPost()
    {
        logger(print_r("called: pullRecentUserPost", true));

        $url = $this->IG_URL . "/" . $this->IG_access_codes->IG_APP_SCOPED_ID . "/media";

        $currentDate = new \DateTime();
        $date2 = clone $currentDate;
        $date2->modify("-" . $this->default_timeFrame);

        $date2_Formatted = $date2->format('Y-m-d');

        $hasNextPage = true;
        $nextPageURL = true;

        $IGUserPostRequest = Http::connectTimeout(60)->timeout(60)->get($url, [
            "fields" => "id,caption,comments_count,like_count,media_url,timestamp,permalink,media_product_type",
            "access_token" => $this->IG_access_codes->long_lived_access_token,
            "since" => strtotime($date2_Formatted),
            "limit" => 100,
        ]);

        $responseData = $IGUserPostRequest->json();

        if (isset($responseData['data'])) {
            $this->allPosts = array_merge($this->allPosts, $responseData['data']);
        }

        if (isset($responseData['paging']['next'])) {
            $nextPageURL = $responseData['paging']['next'];
        } else {
            $hasNextPage = false;
        }


        while ($hasNextPage) {

            $response = Http::connectTimeout(60)->timeout(60)->get($nextPageURL);
            $responseData = $response->json();

            if (isset($responseData['data'])) {
                $this->allPosts = array_merge($this->allPosts, $responseData['data']);
            }

            if (isset($responseData['paging']['next'])) {
                $nextPageURL = $responseData['paging']['next'];
            } else {
                $hasNextPage = false;
            }
        }

        // logger(print_r($responseData['paging'], true));
        // logger(print_r($this->allPosts, true));
        // logger(print_r(count($this->allPosts), true));

        // $this->allPosts = array_slice($this->allPosts, 0, 3);
        // logger(print_r(count($this->allPosts), true));


        $this->savePostData();

        logger('done pullRecentUserPost');

        return $this->commentersProfileAddedForScrapping;
    }

    private function savePostData()
    {
        try {
            //code...
            $email = $this->user->email;

            $user_mongodb = user_mongodb_subprofile::where(['email' => $email])->first();

            for ($i = 0; $i < count($this->allPosts); $i++) {
                $current = $this->allPosts[$i];

                // $ig_bis_post = new ig_business_account_posts();

                $ig_bis_post = ig_business_account_posts::updateOrCreate([
                    "ig_business_account" =>  $this->IG_access_codes->IG_USERNAME,
                    "id" =>  $current["id"],

                ], [
                    "caption" => $current["caption"],
                    "commentsCount" => $current["comments_count"],
                    "likesCount" => $current["like_count"],
                    "displayUrl" => $current["media_url"],
                    "timestamp" => $current["timestamp"],
                    "url" => $current["permalink"],
                    "media_product_type" => $current["media_product_type"],

                ]);

                $user_mongodb->ig_business_account_posts_added()->save($ig_bis_post);

                // logger(print_r($ig_bis_post, true));


                $this->pullPostComment($ig_bis_post);

                // logger(print_r($this->commenters_tracker, true));
            }

            // logger(print_r($this->commenters_tracker, true));

            $this->allPosts = [];
            $this->saveCommentersWithFiveOrMoreCommentsForScraping();
        } catch (\Throwable $th) {
            logger(print_r("Error saving PostData: ", true));
            logger(print_r($th->getMessage(), true));
        }
    }

    public function pullPostComment(ig_business_account_posts $post)
    {
        try {
            //code...
            // logger(print_r("called pullPostComment: ", true));

            $url = $this->IG_URL . "/" . $post->id . "/comments";

            $comments = [];

            $hasNextPage = true;
            $nextPageURL = true;

            $IGPostCommentRequest = Http::connectTimeout(60)->timeout(60)->get($url, [
                "fields" => "id,from,like_count,parent_id,text,timestamp,username,user,replies{id}",
                "access_token" => $this->IG_access_codes->long_lived_access_token,
                "limit" => 50,
            ]);

            $responseData = $IGPostCommentRequest->json();

            if (isset($responseData['data'])) {
                $comments = array_merge($comments, $responseData['data']);
            }

            if (isset($responseData['paging']['next'])) {
                $nextPageURL = $responseData['paging']['next'];
            } else {
                $hasNextPage = false;
            }


            while ($hasNextPage) {
                $response = Http::connectTimeout(60)->timeout(60)->get($nextPageURL);
                $responseData = $response->json();
                // logger(print_r($responseData, true));


                if (isset($responseData['data'])) {
                    $comments = array_merge($comments, $responseData['data']);
                }

                if (isset($responseData['paging']['next'])) {
                    $nextPageURL = $responseData['paging']['next'];
                } else {
                    $hasNextPage = false;
                }
            }

            // logger(print_r($comments, true));
            $this->savePostCommentData($comments, $post);

            $this->exploreReplies($post);
        } catch (\Throwable $th) {
            //throw $th;
            logger(print_r("Error pulling PostComment: ", true));
            logger(print_r($th->getMessage(), true));
        }
    }

    private function savePostCommentData($comments, ig_business_account_posts $post)
    {

        for ($i = 0; $i < count($comments); $i++) {
            $current = $comments[$i];

            // logger($current);

            if (
                !($current["from"]["username"] ?? false) ||
                !($current["text"] ?? false) ||
                !($current["timestamp"] ?? false)
            ) {
                continue;
            }

            // logger('here');

            $new_comment = ig_business_account_post_comments::updateOrCreate([
                "ig_business_account" =>  $this->IG_access_codes->IG_USERNAME,
                "comment_id" =>  $current["id"],

            ], [
                "commenter_ig_username" => $current["from"]["username"],
                "likesCount" => $current["like_count"] ?? 0,
                "text" => $current["text"] ?? '',
                "parent_comment_id" => $current["parent_id"] ?? '',
                "timestamp" => $current["timestamp"],

            ]);

            $post->comments()->save($new_comment);


            if (
                ($current["from"]["username"] ?? '') != $this->IG_access_codes->IG_USERNAME
            ) {
                $commenter = ig_profiles::where('ig_handle', '=', $current["from"]["username"])->first() ?? false;

                if (!$commenter) {
                    $commenter = new ig_profiles();
                    $commenter->ig_handle = $current["from"]["username"];
                    $commenter->save();
                }


                if (($this->commenters_tracker[$current["from"]["username"]] ?? false)) {
                    $this->commenters_tracker[$current["from"]["username"]]++;
                } else {
                    $this->commenters_tracker[$current["from"]["username"]] = 1;
                }

                $commenter->ig_bis_account_posts_commented_on()->attach($post);
                $commenter->comments()->save($new_comment);

                $email = $this->user->email;
                $user_mongodb = user_mongodb_subprofile::where(['email' => $email])->first();
                $user_mongodb->ig_profiles_added()->attach($commenter);
            }


            //  Add to the reply stack if there is a reply
            $replies = $current["replies"] ?? [];

            if (isset($replies['data'])) {
                foreach ($replies['data'] as $key => $value) {
                    if ($value['id']) {
                        array_push($this->repliesIDStack, $value['id']);
                    }
                }
            }
        }
    }

    private function saveCommentersWithFiveOrMoreCommentsForScraping()
    {
        if (count($this->commenters_tracker ?? []) == 0) {
            $this->commentersProfileAddedForScrapping = false;

            return;
        }

        foreach ($this->commenters_tracker as $key => $value) {

            if ($value >= 2) {
                // if ($value >= 5) {
                ig_business_account_post_commenter_to_be_scraped::updateOrCreate([
                    "ig_handle" => $key,
                ], [
                    "ig_handle" =>  $key,
                ]);

                ig_business_account_post_commenter_to_be_scraped::where('ig_handle', $key)
                    ->push(
                        'resulting_ig_business_accounts',
                        [$this->IG_access_codes->IG_USERNAME],
                        unique: true
                    );


                $resulting_ig_data_fetch_processes = $this->IG_access_codes->IG_USERNAME . "%__%" .
                    $this->ig_data_fetch_process_id;

                ig_business_account_post_commenter_to_be_scraped::where('ig_handle', $key)
                    ->push(
                        'resulting_ig_data_fetch_processes',
                        [$resulting_ig_data_fetch_processes],
                        unique: true
                    );
            }
        }

        $this->commentersProfileAddedForScrapping = true;
    }

    private function exploreReplies(ig_business_account_posts $post)
    {
        try {
            //code...
            while (count($this->repliesIDStack) > 0) {
                $current_reply_id = array_shift($this->repliesIDStack);

                // logger(print_r("called exploreReplies: ", true));

                $url = $this->IG_URL . "/" . $current_reply_id;

                $comments = [];

                $IGPostCommentRequest = Http::connectTimeout(60)->timeout(60)->get($url, [
                    "fields" => "id,from,like_count,parent_id,text,timestamp,username,user,replies{id}",
                    "access_token" => $this->IG_access_codes->long_lived_access_token,
                    "limit" => 50,
                ]);

                $responseData = $IGPostCommentRequest->json();

                // logger($responseData);
                // return;

                if (isset($responseData['id'])) {
                    $comments[] =  $responseData;
                }

                // logger("saving reply");

                $this->savePostCommentData($comments, $post);
            }
        } catch (\Throwable $th) {
            //throw $th;
            logger(print_r("Error exploring Replies: ", true));
            logger(print_r($th->getMessage(), true));
        }
    }
}
