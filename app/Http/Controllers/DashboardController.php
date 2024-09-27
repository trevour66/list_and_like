<?php

namespace App\Http\Controllers;

use App\Models\ig_data_fetch_process;
use App\Models\IGAccessCodes;
use App\Dashboard_Analytics\Dashboard_Analytics;

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
                // "IG_data_fetch_process" => $code->igDataFetchProcess()->where('IDFP_status', '=', 'finished_success')->latest()->first() ?? null
            ];

            array_push($igDataFetchProcess, $data);
        }

        // logger($igDataFetchProcess);

        return Inertia::render('Dashboard/Dashboard', [
            "ig_data_fetch_process" => $igDataFetchProcess
        ]);
    }
}
