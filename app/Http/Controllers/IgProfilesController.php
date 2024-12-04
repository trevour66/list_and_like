<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ig_profiles;
use App\Models\user_mongodb_subprofile;
use App\Models\user_list;

use Inertia\Inertia;
use Inertia\Response;
use Error;
use App\Services\ApifyScraper;


class IgProfilesController extends Controller
{
    public function search_api(Request $request)
    {
        try {
            $validated = $request->validate([
                'ig_handle' => 'string|required',
            ]);
            $email = (string) auth()->user()->email;

            $user = user_mongodb_subprofile::where('email', "=", $email)->first() ?? false;

            $ig_profiles = [];

            $ig_profiles = ig_profiles::whereIn('user_mongodb_subprofile_user_ids', [$user->user_id])->where('ig_handle', '=', $validated['ig_handle'])->orderBy('followers', 'desc')->orderBy('bio', 'desc')->cursorPaginate(10);
            // user_mongodb_subprofile_user_ids
            $resData = response(json_encode(
                [
                    'status' => "success",
                    'ig_profiles' => $ig_profiles,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("IgProfilesController API Error (index_api) " . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    'ig_profiles' => null,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    public function index_api(Request $request)
    {
        try {


            $email = (string) auth()->user()->email;

            $user = user_mongodb_subprofile::where('email', "=", $email)->first() ?? false;

            // logger(auth()->user());
            // logger($request->user());
            // logger($email);
            // logger($user);
            $ig_profiles = [];


            $ig_profiles = ig_profiles::whereIn('user_mongodb_subprofile_user_ids', [$user->user_id])->orderBy('followers', 'desc')->orderBy('bio', 'desc')->cursorPaginate(10);
            // user_mongodb_subprofile_user_ids
            $resData = response(json_encode(
                [
                    'status' => "success",
                    'ig_profiles' => $ig_profiles,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("IgProfilesController API Error (index_api) " . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    'ig_profiles' => null,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $email = $request->user()->email;

        $user = user_mongodb_subprofile::where('email', '=', $email)->first() ?? false;

        $user_lists = [];

        if ($user) {
            $user_lists = user_list::where('user_id', '=', $user->user_id)->get() ?? [];
        }

        // logger($user);

        // logger($user_lists);
        return Inertia::render('IG_Profiles', [
            'user_lists' => $user_lists,

        ]);
    }

    public function store(Request $request)
    {

        // $data = [
        //     'account_link' => "test",
        //     'profile_pic' => "test",
        //     'bio' => "test",
        //     'post_count' => "test",
        //     'followers' => "test",
        //     'following' => "test",
        //     'verified' => "test",
        // ];

        // $ig_profiles_update = ig_profiles::where(['ig_handle' => "test"])
        //     ->orderBy('_id')
        //     ->first()
        //     ->update($data);
    }

    public function chrome_extension_add_ig_username(Request $request)
    {
        try {
            $validated = $request->validate([
                'ig_handle' => 'string|required',
                'ig_business_account' => 'string|required',
            ]);

            $email = auth()->user()->email;

            $user = user_mongodb_subprofile::where([
                'email' => $email,
            ])
                ->orderBy('_id')
                ->first() ?? false;

            if (!$user) {
                throw new Error("Could not identify user");
            }

            $scrapper = new ApifyScraper();
            $scrape_response =  $scrapper->scrape_single($validated["ig_handle"], $validated["ig_business_account"]);

            $ig_profile = ig_profiles::where(['ig_handle' => $validated["ig_handle"]])
                ->orderBy('_id')
                ->first() ?? false;


            if (!$ig_profile) {
                throw new Error("Could not add IG Profile");
            }

            $ig_profile->users_ids()->attach($user);
            $ig_profile->directly_added_from_browser_extension_by()->attach($user);
            $user->ig_profiles_added()->attach($ig_profile);

            $resData = response(json_encode([
                'status' => "success",
                "data" => [],
            ]), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "data" => null,
                    "message" => $th->getMessage()
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }
}
