<?php

namespace App\Http\Controllers;

use App\Models\ig_profile_post;
use App\Models\ig_profiles;
use App\Models\user_mongodb_subprofile;
use App\Models\user_list;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Redirect;
use Ramsey\Uuid\Uuid;

class UserListController extends Controller
{
    public function index_api(Request $request)
    {
        try {
            //code...
            $user_lists = [];

            $validated = $request->validate([
                'IG_username' => 'required|string'
            ]);

            $ig_username = $validated['IG_username'];

            $user_lists = user_list::where('ig_business_account', $ig_username)
                ->get() ?? [];

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'user_lists' => $user_lists,
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("My_IG_Posts API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "user_lists" => []
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

        $user_id = $request->user()->id;

        $user_lists = user_list::where(['user_id' => $user_id])->get();

        // logger($user_lists);
        return Inertia::render('User_List/Index', [
            'user_lists' => $user_lists,
            "ig_data_fetch_process" => $igDataFetchProcess,

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            //code...
            $request->validate([
                'list_name' => 'required|string',
                'ig_business_account' => 'required|string',
                'list_description' => 'nullable|string',
            ]);

            //create a webhook receiver
            $list_webhook_id = (string) Uuid::uuid4();
            $list_webhook_id___is_unique = false;

            // logger($list_webhook_id);

            do {
                $exists = user_list::where('list_webhook_id', '=',  $list_webhook_id)->exists();

                if ($exists) {
                    $list_webhook_id = Uuid::uuid4();
                } else {
                    $list_webhook_id___is_unique = true;
                }
            } while (!$list_webhook_id___is_unique);

            $user_id = $request->user()->id;
            $email = auth()->user()->email;

            $user_mongodb = user_mongodb_subprofile::where([
                'email' => $email,
            ])
                ->orderBy('_id')
                ->first() ?? false;


            $list_name = strtolower($request->list_name);

            // Check if the list name is unique for the user
            $exists = user_list::where('list_name',  $list_name)
                ->where('user_id', $user_id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withErrors(['list_name' => 'The list name must be unique. List with the same name exist'])
                    ->withInput();
            }

            $exist = user_list::create([
                'list_name' =>  $list_name,
                'list_description' =>  $request->list_description ?? '',
                'ig_business_account' => $request->ig_business_account,
                'user_id' => $user_id,
                'list_webhook_id' => $list_webhook_id
            ]);

            $user_mongodb->userlists()->save($exist);


            return redirect()->route('user_lists.index')->with('success', 'User list created successfully.');
        } catch (\Throwable $th) {
            logger("Error create user list");
            logger($th->getMessage());
            return redirect()->back()
                ->withErrors(['general' => 'we were not able to process you request please try again later'])
                ->withInput();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, user_list $userList)
    {
        $validated = $request->validate([
            'ig_handle' => 'required|string',
        ]);

        $ig_profile = ig_profiles::where(['ig_handle' => $validated["ig_handle"]])
            ->orderBy('_id')
            ->first() ?? false;

        if (!$ig_profile) {
            throw new \Error("Could not add IG Profile");
        }

        $userList->ig_profiles()->attach($ig_profile);
    }

    /**
     * Display the specified resource.
     */
    public function show(user_list $userList)
    {

        unset($userList->ig_profiles);

        return Inertia::render('User_List/Show', [
            'user_list' => $userList,

        ]);
    }

    public function show_profiles_api(user_list $userList)
    {
        try {
            //code...
            $ig_profiles = $userList->ig_profiles()->cursorPaginate(10);;

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'user_list' => $userList,
                    'ig_profiles' => $ig_profiles
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("show_profiles_api API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    'user_list' => [],
                    'ig_profiles' => [],
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }


    public function show_posts_api(Request $request, user_list $userList)
    {
        try {
            //code...
            $validated = $request->validate([
                'business_account_id' => 'required|string',
            ]);

            $businessAccountId = $validated['business_account_id'];

            $associated_user_posts = [];

            $ig_usernames = [$businessAccountId];

            $associated_profiles = $userList->ig_profiles;
            // logger($associated_profiles);

            $associated_profiles = collect($associated_profiles ?? [])->pluck('ig_handle') ?? [];

            $associated_user_posts = ig_profile_post::whereNotIn('skipped_by', $ig_usernames)
                ->whereNotIn('reactedTo_by', $ig_usernames)
                ->orderBy('timestamp', 'desc')
                ->orderBy('updated_at', 'desc')
                ->whereIn('ig_profile_handle', $associated_profiles)
                ->cursorPaginate(10);

            unset(
                $userList->ig_profiles
            );

            $resData = response(json_encode(
                [
                    'status' => "success",
                    'user_list' => $userList,
                    'associated_user_posts' => $associated_user_posts
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("show_profiles_api API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    'user_list' => [],
                    'associated_user_posts' => [],
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    public function delete_IG_profile_from_list(Request $request, user_list $userList)
    {
        try {
            //code...
            $validated = $request->validate([
                'ig_profile_id' => 'required|string',
            ]);

            $ig_profile_id = $validated['ig_profile_id'];

            $userList->ig_profiles()->detach($ig_profile_id);

            $resData = response(json_encode(
                [
                    'status' => "success",

                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Throwable $th) {
            logger("delete_IG_profile_from_list API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",

                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, user_list $userList)
    {
        // Validate incoming request
        $validated = $request->validate([
            'list_name' => "required|string",
            'list_description' => 'nullable|string',
        ]);

        // Perform manual uniqueness check for 'list_name'
        if ($request->has('list_name') && user_list::where('list_name', $request->list_name)->where('_id', '!=', $userList->_id)->exists()) {
            return redirect()->back()
                ->withErrors(['list_name' => 'The list name must be unique. List with the same name exist'])
                ->withInput();
        }

        // Update the record with validated data
        $userList->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user_list $userList)
    {

        $userList->delete();
        return Redirect::to('/my-lists');
    }
}
