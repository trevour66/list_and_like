<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\IGAccessCodes;
use Error;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function verify(Request $request)
    {
        logger('called verify webhook');

        // Define the verification token (you can set it in the .env file)
        $token = env('WEBHOOK_VERIFY_TOKEN');

        // Check the query parameters to verify the webhook
        if (
            $request->query('hub_mode') === 'subscribe' &&
            $request->query('hub_verify_token') === $token
        ) {
            return response($request->query('hub_challenge'));
        }

        return response()->json(['error' => 'Unauthorized'], 400);
    }

    public function handle(Request $request)
    {
        logger('called handle webhook');

        $data = $request->all();

        logger($data);

        // // Ensure the incoming request has the necessary data
        // if (!isset($data['entry'][0]['changes'][0]['value'])) {
        //     return response()->json(['error' => 'Invalid data'], 400);
        // }

        // $commentData = $data['entry'][0]['changes'][0]['value'];
        // $commenterId = $commentData['from']['id'];
        // $commentText = $commentData['text'];
        // $parentCommentId = $commentData['parent_id'] ?? null; // Check if it’s a reply

        // // Retrieve the IG App user ID
        // $appUserId = config('services.instagram.app_user_id'); // Set your IG app user ID in env file

        // // Disregard if the commenter is the same as the app user
        // if ($commenterId == $appUserId) {
        //     return response()->json(['message' => 'Ignored comment from app user'], 200);
        // }

        // // Process and save the comment
        // $comment = new Comment([
        //     'commenter_id' => $commenterId,
        //     'text' => $commentText,
        //     'parent_comment_id' => $parentCommentId,
        // ]);
        // $comment->save();

        // // Notify the app user of a new comment
        // $user = User::find($appUserId); // Adjust as per app logic
        // Notification::send($user, new NewCommentNotification($comment));

        return response()->json(['message' => 'Comment processed'], 200);
    }

    static function subscribeToWebhook(IGAccessCodes $IG_access_codes)
    {
        try {
            logger(print_r("called: subscribeToWebhook", true));
            $IGGetAccountURL = "https://graph.instagram.com/v21.0";

            $url = $IGGetAccountURL . "/" . $IG_access_codes->IG_APP_SCOPED_ID . "/subscribed_apps";

            $IGWebhookSubscriptionRequest = Http::connectTimeout(60)->timeout(60)->post($url, [
                "subscribed_fields" => "comments",
                "access_token" => $IG_access_codes->long_lived_access_token,
            ]);

            $responseData = $IGWebhookSubscriptionRequest->json() ?? [];

            logger($responseData);

            if (! isset($responseData['success'])) {
                throw new Error('Subscribe to webhook request did not return a response with success field');
            }

            if (! $responseData['success']) {
                throw new Error('Subscribe to webhook request returned a response with success field but success field is false');
            }

            // $IG_access_codes->update([
            //     'webhook_status' => 'active'
            // ]);

            $IG_access_codes->webhook_status = 'active';
            $IG_access_codes->save();

            return true;
        } catch (\Exception $th) {
            logger($th->getMessage());
            return false;
        }
    }

    static function delete_subscriptionToWebhook(IGAccessCodes $IG_access_codes)
    {
        try {
            //             DELETE /v21.0/{app-id}/subscriptions HTTP/1.1
            // Host: graph.facebook.com
            logger(print_r("called: subscribeToWebhook", true));
            $IGGetAccountURL = "https://graph.instagram.com/v21.0";

            $url = $IGGetAccountURL . "/" . $IG_access_codes->IG_APP_SCOPED_ID . "/subscribed_apps";

            $IGWebhookSubscriptionRequest = Http::connectTimeout(60)->timeout(60)->delete($url, [
                "subscribed_fields" => "comments",
                "access_token" => $IG_access_codes->long_lived_access_token,
            ]);

            $responseData = $IGWebhookSubscriptionRequest->json() ?? [];

            logger($responseData);

            if (! isset($responseData['success'])) {
                throw new Error('Subscribe to webhook request did not return a response with success field');
            }

            if (! $responseData['success']) {
                throw new Error('Subscribe to webhook request returned a response with success field but success field is false');
            }

            // $IG_access_codes->update([
            //     'webhook_status' => 'active'
            // ]);

            $IG_access_codes->webhook_status = 'inactive';
            $IG_access_codes->save();

            return true;
        } catch (\Exception $th) {
            logger($th->getMessage());
            return false;
        }
    }
}
