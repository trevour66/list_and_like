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

    private $parentsStack = [];
    private $repliesCollection = [];

    public function __construct(
        ig_business_account_posts $ig_business_account_posts,
        IGAccessCodes $IG_access_codes,
    ) {
        $this->ig_business_account_posts = $ig_business_account_posts;
        $this->IG_access_codes = $IG_access_codes;
    }

    public function createNewComment($message)
    {
        try {
            logger(print_r("called: createNewComment", true));

            $url = $this->IG_URL . "/" . $this->ig_business_account_posts->id . "/comments?";

            $url = $url . http_build_query([
                'message' => $message,
                "access_token" => $this->IG_access_codes->long_lived_access_token,
            ]);

            $newIGCommentRequest = Http::connectTimeout(100)->timeout(100)->post($url);

            $responseData = $newIGCommentRequest->json();

            // logger($newIGCommentRequest->status());
            // logger($responseData);
            // logger($url);

            $statusOfRequest = $newIGCommentRequest->status();
            $idOfNewlyAddedComment = $responseData["id"] ?? false;

            if (
                !$idOfNewlyAddedComment ||
                $statusOfRequest != 200
            ) {
                throw new Error('Error sending comment to IG');
            }

            return $idOfNewlyAddedComment;
        } catch (\Throwable $th) {
            logger("Error createNewComment");
            logger($th->getMessage());
            return false;
        }
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
                ->connectTimeout(100)
                ->timeout(100)
                ->post($url, [
                    "message" => $message,
                ]);


            $responseData = $replyToCommentRequest->json();

            // logger($replyToCommentRequest->status());
            // logger($responseData);
            // logger($url);

            // logger('done replyOtherComment');

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

        logger('done addCommentToDatabase');
    }

    public function getAllRelatedReplies($parent_comment_id)
    {
        logger(print_r("called: getAllRelatedReplies", true));

        array_push($this->parentsStack, $parent_comment_id);

        // logger($this->parentsStack);

        $this->exploreTree();

        // logger($this->repliesCollection);
        // logger(count($this->repliesCollection));
        logger('done getAllRelatedReplies');

        if (count(($this->repliesCollection ?? [])) == 0) {
            return $this->repliesCollection;
        }

        $repliesCollection = collect($this->repliesCollection);

        $repliesCollection->sortBy('timestamp');

        return $repliesCollection;
    }

    private function exploreTree()
    {
        while (count($this->parentsStack) > 0) {
            $current_parent_comment_id = array_shift($this->parentsStack);

            $current_children_comments = ig_business_account_post_comments::where("parent_comment_id", "=", $current_parent_comment_id)->without('children')->get() ?? [];

            // logger($current_children_comments);

            foreach ($current_children_comments as $key => $value) {
                array_push($this->repliesCollection, $value);
                array_push($this->parentsStack, $value->comment_id);
            }

            // logger($this->parentsStack);
        }
    }
}
