<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ig_profiles;
use App\Models\user_mongodb_subprofile;
use App\Models\user_list;

use Inertia\Inertia;
use Inertia\Response;
use Error;

class IgProfilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $email = $request->user()->email;

        $user = user_mongodb_subprofile::where(['email' => $email])->first() ?? false;
        $user_lists = [];
        $ig_profiles = [];

        if ($user && $user->id) {
            $user_lists = user_list::where(['user_id' => $user->id])->get() ?? [];
        }

        if (count(($user_lists ?? []))) {
            $ig_profiles = $user_lists->ig_profiles_added ?? [];
        }

        return Inertia::render('IG_Profiles', [
            'ig_profiles' => $ig_profiles,
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
            ]);

            $user_id = auth()->user()->id;
            $email = auth()->user()->email;

            $user = user_mongodb_subprofile::where([
                'email' => $email,
            ])
                ->orderBy('_id')
                ->first() ?? false;

            if (!$user) {
                $user = new user_mongodb_subprofile();
                $user->fill([
                    'user_id' => $user_id, 'email' => $email,

                ]);
                $user->save();
            }

            $ig_profile = ig_profiles::where(['ig_handle' => $validated["ig_handle"]])
                ->orderBy('_id')
                ->first() ?? false;

            if (!$ig_profile) {
                $data = [
                    'ig_handle' => $validated["ig_handle"],
                    'account_link' => "",
                    'profile_pic' => "",
                    'bio' => "",
                    'post_count' => 0,
                    'followers' => 0,
                    'following' => 0,
                    'verified' => false,
                    'posts' => []
                ];

                $ig_profile = new ig_profiles();
                $ig_profile->fill($data);
                $ig_profile->save();
            }

            if (!$ig_profile) {
                throw new Error("Could not add IG Profile");
            }

            $ig_profile->users_ids()->attach($user);
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
