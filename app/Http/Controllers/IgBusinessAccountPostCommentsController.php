<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ig_business_account_post_comments;
use App\Models\ig_business_account_posts;
use App\Models\IGAccessCodes;
use App\Services\IGMediaCommentsModeration;
use Error;

class IgBusinessAccountPostCommentsController extends Controller
{
    public function get_comments_api(Request $request)
    {
        try {
            //code...
            $associated_comments = [];

            $validated = $request->validate([
                'post_id' => 'required|string'
            ]);

            $ig_business_account_posts_id = $validated['post_id'];

            $associated_comments = ig_business_account_post_comments::where('ig_business_account_posts_id', $ig_business_account_posts_id)
                ->where('parent_comment_id', '')
                ->orderBy('timestamp', 'desc')
                ->orderBy('updated_at', 'desc')
                ->cursorPaginate(10);

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'associated_comments' => $associated_comments,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("get_comments_api API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "associated_comments" => null
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    public function new_comment_api(Request $request)
    {
        try {

            $validated = $request->validate([
                'post_id' => 'required|string',
                'message' => 'required|string',
                'from' => 'required|string'
            ]);

            $ig_business_account_posts_id = $validated['post_id'];
            $message = $validated['message'];
            $from = $validated['from'];

            $associated_post = ig_business_account_posts::where('_id', $ig_business_account_posts_id)->first() ?? false;
            $associated_post_owner = IGAccessCodes::where('IG_USERNAME', $from)->first() ?? false;

            if (
                !$associated_post || !$associated_post_owner
            ) {
                throw new Error('Associated Post not found');
            }

            $IGMediaCommentsModeration = new IGMediaCommentsModeration($associated_post, $associated_post_owner);
            $idOfNewlyAddedComment = $IGMediaCommentsModeration->createNewComment($message);

            if ($idOfNewlyAddedComment) {
                $IGMediaCommentsModeration->addCommentToDatabase($message, $idOfNewlyAddedComment, $from, '');
            } else {
                throw new Error("Comment was not sent to IG");
            }

            $resData = response(json_encode(
                [
                    'status' => "success",
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("reply_to_comment_api API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "error" => $th->getMessage()
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    public function reply_to_comment_api(Request $request)
    {
        try {

            $validated = $request->validate([
                'ig_comment_replying_to' => 'required|string',
                'message' => 'required|string',
                'post_id' => 'required|string',
                'from' => 'required|string',
            ]);

            $ig_business_account_posts_id = $validated['post_id'];
            $ig_comment_replying_to = $validated['ig_comment_replying_to'];
            $message = $validated['message'];
            $from = $validated['from'];

            $associated_post = ig_business_account_posts::where('_id', $ig_business_account_posts_id)->first() ?? false;
            $associated_post_owner = IGAccessCodes::where('IG_USERNAME', $from)->first() ?? false;

            if (!$associated_post || !$associated_post_owner) {
                throw new Error('Associated Post not found');
            }

            $IGMediaCommentsModeration = new IGMediaCommentsModeration($associated_post, $associated_post_owner);
            $idOfNewlyAddedComment = $IGMediaCommentsModeration->replyOtherComment($message, $ig_comment_replying_to);

            if ($idOfNewlyAddedComment) {
                $IGMediaCommentsModeration->addCommentToDatabase($message, $idOfNewlyAddedComment, $from, $ig_comment_replying_to);
            } else {
                throw new Error("Comment was not sent to IG");
            }

            $resData = response(json_encode(
                [
                    'status' => "success",
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("reply_to_comment_api API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "error" => $th->getMessage()
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    public function get_all_comment_replies_api(Request $request)
    {
        try {

            $validated = $request->validate([
                'post_id' => 'required|string',
                'from' => 'required|string',
                'parent_comment_id' => 'required|string'
            ]);

            $parent_comment_id = $validated['parent_comment_id'];
            $ig_business_account_posts_id = $validated['post_id'];
            $from = $validated['from'];

            $associated_post = ig_business_account_posts::where('_id', $ig_business_account_posts_id)->first() ?? false;
            $associated_post_owner = IGAccessCodes::where('IG_USERNAME', $from)->first() ?? false;

            if (!$associated_post || !$associated_post_owner) {
                throw new Error('Associated Post not found');
            }

            $IGMediaCommentsModeration = new IGMediaCommentsModeration($associated_post, $associated_post_owner);
            $repliesCollection = $IGMediaCommentsModeration->getAllRelatedReplies($parent_comment_id);

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'replies' => $repliesCollection
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("get_all_comment_replies_api API Error " . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    'replies' => []
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }
}
