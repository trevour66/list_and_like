<?php

namespace App\Http\Controllers;

use App\Models\ig_profile_post;
use App\Models\user_mongodb_subprofile;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommunityController extends Controller
{
    //

    public function index_api(Request $request)
    {
        try {
            //code...
            $user = $request->user();
            $associated_user_posts = [];

            $ig_usernames = $user->IGAccessCodes()->pluck('IG_USERNAME')->toArray() ?? [];

            // logger(print_r($ig_usernames, true));

            if (
                (count($ig_usernames) > 0)
            ) {
                $associated_user_posts = ig_profile_post::whereIn('associated_ig_business_accounts', $ig_usernames)
                    ->orderBy('timestamp', 'desc')
                    ->orderBy('updated_at', 'desc')
                    ->cursorPaginate(10);
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
            logger("Community API Error" . $th->getMessage());
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

    public function index(Request $request)
    {
        $user = $request->user();

        $IGAccessCodes = $user->IGAccessCodes ?? [];

        // logger(print_r($IGAccessCodes, true));

        return Inertia::render('Community/Index', [
            "IGAccessCodes" => $IGAccessCodes
        ]);
    }
}
