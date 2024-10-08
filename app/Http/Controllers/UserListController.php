<?php

namespace App\Http\Controllers;

use App\Models\ig_profiles;
use App\Models\user_list;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Redirect;


class UserListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $user_id = $request->user()->id;

        $user_lists = user_list::where(['user_id' => $user_id])->get();

        return Inertia::render('User_List/Index', [
            'user_lists' => $user_lists
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'list_name' => 'required|string',
            'list_description' => 'nullable|string',
        ]);

        $user_id = $request->user()->id;
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

        user_list::create([
            'list_name' =>  $list_name,
            'list_description' =>  $request->list_description ?? '',
            'user_id' => $user_id,
        ]);

        return redirect()->route('user_lists.index')->with('success', 'User list created successfully.');
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
        $ig_profiles = $userList->ig_profiles;

        logger($userList);
        logger($ig_profiles);

        return Inertia::render('User_List/Show', [
            'user_list' => $userList,
            'ig_profiles' => $ig_profiles
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(user_list $userList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, user_list $userList)
    {
        //
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
