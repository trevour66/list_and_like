<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\IGAccessCodes;
use App\Models\ig_business_account_posts;
use App\Models\ig_business_account_post_comments;
use App\Models\ig_profiles;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use App\Models\user_list;
use App\Services\ApifyScraper;


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
        try {
            //code...
            logger('called handle webhook');

            $data = $request->all();

            if (!isset($data['entry'][0]['changes'][0]['value'])) {
                return response()->json(['error' => 'Invalid data'], 400);
            }

            // logger($data);

            // Check if 'entry' and 'changes' are present in the data
            if (isset($data['entry']) && is_array($data['entry'])) {
                foreach ($data['entry'] as $entry) {

                    if (!isset($entry['time'])) {
                        logger('Time not in data');
                        continue;
                    }

                    $timestamp = $entry['time'] ?? null;
                    $carbonDate = Carbon::createFromTimestamp($timestamp);
                    $formattedDate = $carbonDate->format("Y-m-d\TH:i:s.vP");

                    if (isset($entry['changes']) && is_array($entry['changes'])) {
                        foreach ($entry['changes'] as $change) {
                            if (!isset($change['value']) && !is_array($change['value'])) {
                                logger('Value Empty');
                                continue;
                            }

                            // Extract data safely using null coalescence or ternary operators
                            $fromId = $change['value']['from']['id'] ?? null;
                            $fromUsername = $change['value']['from']['username'] ?? null;
                            $mediaId = $change['value']['media']['id'] ?? null;
                            $mediaProductType = $change['value']['media']['media_product_type'] ?? null;
                            $commentIG_Id = $change['value']['id'] ?? null;
                            $commentIG_parentId = $change['value']['parent_id'] ?? null;
                            $text = $change['value']['text'] ?? null;

                            $commenter = ig_profiles::where('ig_handle', '=', $fromUsername)->first() ?? false;
                            $parent_comment = ig_business_account_post_comments::where('comment_id', '=', $commentIG_parentId)->first() ?? false;
                            $ig_business_account_posts = ig_business_account_posts::where('id', '=', $mediaId)->first() ?? false;

                            if (!$commenter || !$ig_business_account_posts) {
                                logger('comment or the IG_business_post not found');
                                continue;
                            }

                            $new_comment = ig_business_account_post_comments::updateOrCreate([
                                "ig_business_account_posts_id" =>  $mediaId,
                                "comment_id" =>  $commentIG_Id

                            ], [
                                "commenter_ig_username" => $fromUsername,
                                "likesCount" => 0,
                                "text" => $text ?? '',
                                "parent_comment_id" => ($parent_comment) ? $commentIG_parentId : '',
                                "timestamp" => $formattedDate,
                            ]);
                        }
                    }
                }
            }

            return response()->json(['message' => 'Comment processed'], 200);
        } catch (\Throwable $th) {
            logger('Error handle webhook');

            logger($th->getMessage());
            return response()->json(['message' => 'Comment processed'], 200);
        }
    }

    static function subscribeToWebhook(IGAccessCodes $IG_access_codes)
    {
        try {
            logger(print_r("called: subscribeToWebhook", true));
            $IGGetAccountURL = "https://graph.instagram.com/v21.0";

            $url = $IGGetAccountURL . "/" . $IG_access_codes->IG_APP_SCOPED_ID . "/subscribed_apps?";

            $subscribed_fields = 'comments'; // List of fields to subscribe to

            $url = $url . http_build_query([
                'subscribed_fields' => $subscribed_fields,
                'access_token' => $IG_access_codes->long_lived_access_token,
            ]);

            // logger($url);

            $IGWebhookSubscriptionRequest = Http::connectTimeout(60)->timeout(60)->post($url);

            $responseData = $IGWebhookSubscriptionRequest->json() ?? [];

            // logger($responseData);

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

            // logger($responseData);

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

    public function ingest_ighandle_webhook_entry(Request $request, string $list_webhook_id)
    {

        try {
            // Validate the request
            $validatedData = $request->validate([
                'ig_handle' => 'required|string', // Ensure 'ig_handle' is provided
            ]);

            $igHandle = $validatedData['ig_handle'];

            $user_list = user_list::where('list_webhook_id', '=',  $list_webhook_id)->first() ?? null;

            if ($user_list === null) {
                throw new Error('User list does not exist');
            }

            $ig_business_account = $user_list->ig_business_account ?? null;

            if ($ig_business_account === null) {
                throw new Error('User list does not have an attached ig_business_account');
            }

            $scrapper = new ApifyScraper();

            $scrape_response =  $scrapper->scrape_single($igHandle, $ig_business_account);

            $ig_profile = ig_profiles::where(['ig_handle' => $igHandle])
                ->orderBy('_id')
                ->first() ?? false;

            $user_list->ig_profiles()->attach($ig_profile);
            $ig_profile->directly_added_through_a_list_webhook()->attach($user_list);

            return response()->json([
                'status' => 'success',
                'message' => 'Instagram handle processed successfully.',
            ], 200);
        } catch (\Exception $e) {
            // Log the exception
            logger('Error processing Instagram handle:');
            logger($e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing the Instagram handle.',
            ], 500);
        }
    }
}
