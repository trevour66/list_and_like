<?php

namespace App\Http\Controllers;

use App\Models\ig_data_fetch_process;
use App\Models\IGAccessCodes;
use App\Models\ig_profile_post;
use App\Dashboard_Analytics\Dashboard_Analytics;
use App\Dashboard_Analytics\EngagementService;

use Illuminate\Http\Request;
use Inertia\Inertia;




class DashboardController extends Controller
{
    public function fetch_account_analytics_data(Request $request)
    {
        try {
            $validated = $request->validate([
                'IG_username' => 'required|string'
            ]);

            $user = $request->user();
            $user_id = $user->id;
            $user_email = $user->email;

            $analyser = new Dashboard_Analytics(
                $user_id,
                $user_email,
                // 1,
                // 'peteriniubong_list_and_like@gmail.com',
                $validated['IG_username']
            );
            $engagementService = new EngagementService($validated['IG_username']);

            $analyser->calculateData();

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'data' => [
                        "posts_processed" => $analyser->allIGBusinessPostProcessed,
                        "comments_processed" => $analyser->allIGBusinessPostCommentsProcessed,
                        "posts_from_commenters_processed" => $analyser->allIGProfilePostsFromCommentersProcessed,
                        "posts_from_commenters_processed_skipped" => $analyser->allIGProfilePostsFromCommentersProcessed_skipped,
                        "posts_from_commenters_processed_reactedTo" => $analyser->allIGProfilePostsFromCommentersProcessed_reactedTo,
                        "all_IG_profiles_linked_to_IG_business_account" => $analyser->allIGProfilesLinkedToIGBusinessAccount,
                        "all_user_lists" => $analyser->allUserLists,
                        'highest_engagement_profiles' => $engagementService->getHighestEngagers(),

                    ],
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("fetch_account_analytics_data API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "data" => null
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }


    public function fetch_community_data(Request $request)
    {
        try {
            $validated = $request->validate([
                'IG_username' => 'required|string'
            ]);

            $user = $request->user();
            $associated_user_posts = [];

            $ig_usernames = [$validated['IG_username']];

            // logger(print_r($ig_usernames, true));

            if (
                (count($ig_usernames) > 0)
            ) {
                $associated_user_posts = ig_profile_post::whereIn('associated_ig_business_accounts', $ig_usernames)
                    ->whereNotIn('skipped_by', $ig_usernames)
                    ->whereNotIn('reactedTo_by', $ig_usernames)
                    ->orderBy('timestamp', 'desc')
                    ->orderBy('updated_at', 'desc')
                    ->cursorPaginate(10);
            }

            // logger(print_r($associated_user_posts, true));


            foreach ($associated_user_posts as $post) {
                // logger(print_r($post->owner_ig_profile, true));
                $post["owner_ig_profile"] = $post->owner_ig_profile;

                $lists_ig_profile = $post->owner_ig_profile->lists()->where('user_id', '=', $user->id)->get() ?? null;
                $post["lists_ig_profile"] = ($lists_ig_profile) ? $lists_ig_profile->pluck("_id")->toArray() ?? [] : [];
            }


            $resData = response(json_encode(
                [
                    'status' => "success",
                    'associated_user_posts' => $associated_user_posts,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("fetch_community_data API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "associated_user_posts" => null
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    public function sync_data(Request $request)
    {
        //code...
        $validated = $request->validate([
            'IG_account_id' => 'required|numeric',
        ]);

        $ig_account_entry = IGAccessCodes::where('id', '=', $validated['IG_account_id'])->first() ?? null;

        $igDataFetchProcess = $ig_account_entry->igDataFetchProcess()->where('IDFP_status', '=', 'processing')->first() ?? null;

        // logger(print_r($igDataFetchProcess,  true));
        // logger(print_r($ig_account_entry,  true));

        if ($igDataFetchProcess !== null) {
            return;
        }

        $igDataFetchProcess_new = new ig_data_fetch_process(['IDFP_status' => 'processing']);
        $ig_account_entry->igDataFetchProcess()->save($igDataFetchProcess_new);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $IGAccessCodes = $user->IGAccessCodes ?? [];
        $igDataFetchProcess = [];

        foreach ($IGAccessCodes as $code) {
            $data = [
                "IG_account_id" => $code["id"] ?? '',
                "IG_username" => $code["IG_USERNAME"] ?? '',
                "IG_data_fetch_process" => $code->igDataFetchProcess()->latest()->first() ?? null
            ];

            array_push($igDataFetchProcess, $data);
        }

        // logger($igDataFetchProcess);

        return Inertia::render('Dashboard/Dashboard', [
            "ig_data_fetch_process" => $igDataFetchProcess
        ]);
    }
}
