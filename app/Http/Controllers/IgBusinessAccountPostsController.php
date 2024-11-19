<?php

namespace App\Http\Controllers;

use App\Models\ig_business_account_posts;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IgBusinessAccountPostsController extends Controller
{
    public function index_api(Request $request)
    {
        try {
            //code...
            $user = $request->user();
            $associated_IG_posts = [];

            $validated = $request->validate([
                'IG_username' => 'required|string'
            ]);

            $ig_username = $validated['IG_username'];

            $associated_IG_posts = ig_business_account_posts::where('ig_business_account', $ig_username)
                ->orderBy('timestamp', 'desc')
                ->orderBy('updated_at', 'desc')
                ->cursorPaginate(10);

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'associated_IG_posts' => $associated_IG_posts,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("My_IG_Posts API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "associated_IG_posts" => null
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
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

        return Inertia::render('My_IG_Posts/My_IG_Posts', [
            "ig_data_fetch_process" => $igDataFetchProcess
        ]);
    }
}
