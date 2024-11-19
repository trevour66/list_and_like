<?php

namespace App\Services;

use App\Models\ig_business_account_posts;
use App\Models\ig_business_account_post_comments;
use App\Models\ig_profiles;
use App\Models\User;
use App\Models\IGAccessCodes;
use Carbon\Carbon;
use Error;
use Illuminate\Support\Facades\Http;


class IGMediaCommentsModeration
{

    private ?ig_business_account_posts $ig_business_account_posts = null;
    private ?IGAccessCodes $IG_access_codes = null;


    private $IG_URL = 'https://graph.instagram.com';
    private $version = 'v21.0';

    public function __construct(
        ig_business_account_posts $ig_business_account_posts,
        IGAccessCodes $IG_access_codes,
    ) {
        $this->ig_business_account_posts = $ig_business_account_posts;
        $this->IG_access_codes = $IG_access_codes;
    }

    public function createNewComment($message)
    {
        logger(print_r("called: createNewComment", true));

        $url = $this->IG_URL . "/" . $this->ig_business_account_posts->id . "/comments";

        $url = $url . http_build_query([
            'message' => $message,
        ]);

        $newIGCommentRequest = Http::connectTimeout(60)->timeout(60)->post($url);

        $responseData = $newIGCommentRequest->json();

        logger($responseData);

        return;

        // if (isset($responseData['data'])) {
        //     $this->allPosts = array_merge($this->allPosts, $responseData['data']);
        // }


        logger('done createNewComment');
    }

    public function replyOtherComment($message, $comment_replyingTo)
    {
        try {

            logger(print_r("called: replyOtherComment", true));
            $url = $this->IG_URL . "/" . $this->version . "/" . $comment_replyingTo . "/replies?";

            $url = $url . http_build_query([
                "access_token" => $this->IG_access_codes->long_lived_access_token,
            ]);

            $replyToCommentRequest = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
                ->connectTimeout(600)
                ->timeout(600)
                ->post($url, [
                    "message" => $message,
                ]);


            $responseData = $replyToCommentRequest->json();

            logger($replyToCommentRequest->status());
            logger($responseData);
            logger($url);

            logger('done replyOtherComment');

            $statusOfRequest = $replyToCommentRequest->status();
            $idOfNewlyAddedComment = $responseData["id"] ?? false;

            if (
                !$idOfNewlyAddedComment ||
                $statusOfRequest != 200
            ) {
                throw new Error('Error sending comment to IG');
            }

            return $idOfNewlyAddedComment;
        } catch (\Throwable $th) {
            logger("Error replyOtherComment");
            logger($th->getMessage());
            return false;
        }
    }

    public function addCommentToDatabase($message, $commentIG_Id, $fromUsername, $parent_comment)
    {
        logger(print_r("called: addCommentToDatabase", true));

        $timestamp = new Carbon();
        $carbonDate = Carbon::createFromTimestamp($timestamp->timestamp);
        $formattedDate = $carbonDate->format("Y-m-d\TH:i:s.vP");

        $new_comment = ig_business_account_post_comments::updateOrCreate([
            "ig_business_account_posts_id" =>  $this->ig_business_account_posts->_id,
            "comment_id" =>  $commentIG_Id

        ], [
            "commenter_ig_username" => $fromUsername,
            "likesCount" => 0,
            "text" => $message,
            "parent_comment_id" => ($parent_comment) ? $parent_comment : '',
            "timestamp" => $formattedDate,
        ]);

        $this->ig_business_account_posts->comments()->save($new_comment);

        return;

        logger('done addCommentToDatabase');
    }
}
