<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;



class ProfileController extends Controller
{

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $userHasAccessCode = $request->user()->IGAccessCodes()->get() ?? [];
        $userAccessCodes = [];

        if (count($userHasAccessCode) > 0) {


            for ($i = 0; $i < count($userHasAccessCode); $i++) {
                array_push($userAccessCodes, [
                    'IG_APP_SCOPED_ID' => $userHasAccessCode[$i]['IG_APP_SCOPED_ID'],
                    'IG_USERNAME' => $userHasAccessCode[$i]['IG_USERNAME'],
                    'short_lived_access_token' => $userHasAccessCode[$i]['short_lived_access_token'],
                    'long_lived_access_token' => $userHasAccessCode[$i]['long_lived_access_token'],
                    'long_lived_expires_in' => $userHasAccessCode[$i]['long_lived_expires_in'],

                    'created_at' => $userHasAccessCode[$i]['created_at'] ?? false,
                    'updated_at' => $userHasAccessCode[$i]['updated_at'] ?? false,
                ]);
            }
        }

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'userAccessCodes' => $userAccessCodes
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
