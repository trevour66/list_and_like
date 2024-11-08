<?php

namespace App\Http\Controllers;

use App\Models\authorizationRequests;
use App\Models\user_mongodb_subprofile;
use App\Models\ig_data_fetch_process;
use App\Models\IGAccessCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\WebhookController;
use Error;
use Illuminate\Support\Facades\DB;



class IGBusinessLoginController extends Controller
{

    private function getUserLongLivedToken($access_token)
    {
        $IGGetLongLivedTokenURL = "https://graph.instagram.com/access_token";
        $client_secret = config('ig.IG_CLIENT_SECRET');

        $IGGetLongLivedTokenRequest = Http::connectTimeout(60)->timeout(60)->get($IGGetLongLivedTokenURL, [
            "grant_type" => "ig_exchange_token",
            "access_token" => $access_token,
            "client_secret" => $client_secret
        ]);

        logger("getUserLongLivedToken");
        logger($IGGetLongLivedTokenRequest->json());
        logger($IGGetLongLivedTokenRequest->status());

        if (
            !$IGGetLongLivedTokenRequest->ok() ||
            $IGGetLongLivedTokenRequest->json("code") == 100
        ) {
            throw new Error("getUserIGAccountName Error");
        }


        $longLivedTokenData = [
            "access_token" => $IGGetLongLivedTokenRequest->json("access_token"),
            "token_type" => $IGGetLongLivedTokenRequest->json("token_type"),
            "expires_in" => $IGGetLongLivedTokenRequest->json("expires_in"),
        ];

        return $longLivedTokenData;
    }

    private function getUserIGAccountName($access_token)
    {
        $IGGetAccountURL = "https://graph.instagram.com/v20.0/me";

        $IGGetAccountRequest = Http::connectTimeout(60)->timeout(60)->get($IGGetAccountURL, [
            "fields" => "user_id,username ",
            "access_token" => $access_token
        ]);


        logger("getUserIGAccountName");
        logger($IGGetAccountRequest->json());
        logger($IGGetAccountRequest->status());

        if (
            !$IGGetAccountRequest->ok() ||
            $IGGetAccountRequest->json("code") == 100
        ) {
            throw new Error("getUserIGAccountName Error");
        }

        return $IGGetAccountRequest->json("username") ?? false;
    }

    private function syncData($ig_account_entry, $user)
    {
        // logger(print_r($ig_account_entry,  true));
        $igDataFetchProcess = ig_data_fetch_process::where('IDFP_status', '=', 'processing')
            ->where('IDFP_ig_bussines_account', '=' . $ig_account_entry->id)
            ->first() ?? null;

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
        try {

            DB::beginTransaction();

            //code...
            $code = $request->query('code') ?? false;
            $error = $request->query('error') ?? false;
            $state_request_id = $request->query('state');

            if ($error) {
                throw new \Error('CODE_NOT_SAVED | An error occured');
            }

            if (!$code) {
                throw new \Error('CODE_NOT_SAVED | Code not return. It is possible that an error occured');
            }

            $authorizationRequest = authorizationRequests::where('request_id', '=', $state_request_id)->first() ?? false;

            if (!$authorizationRequest) {
                throw new \Error('CODE_NOT_SAVED | Request ID not found. System not able to identify user');
            }

            $current_user = $request->user();

            $IGTokenURL = "https://api.instagram.com/oauth/access_token";


            $client_id = config('ig.IG_CLIENT_ID');
            $client_secret = config('ig.IG_CLIENT_SECRET');
            $grant_type = 'authorization_code';
            $redirect_url = config('ig.IG_REDIRECT_URL');


            $accessTokenRequest = Http::connectTimeout(60)->timeout(60)->asForm()->post($IGTokenURL, [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_url,
                'grant_type' => $grant_type,
                'code' => $code,
            ]);

            logger("index");
            logger($accessTokenRequest->json());
            logger($accessTokenRequest->status());

            $access_token = $accessTokenRequest["access_token"] ?? false;
            $IG_APP_SCOPED_ID = $accessTokenRequest["user_id"] ?? false;
            $permissions = $accessTokenRequest["permissions"] ?? [];

            // logger(print_r($access_token, true));
            // logger(print_r($IG_APP_SCOPED_ID, true));
            // logger(print_r($permissions, true));

            if (
                !$access_token ||
                !$IG_APP_SCOPED_ID ||
                (count($permissions) <= 0)
            ) {
                throw new \Error('CODE_NOT_SAVED | some details missing');
            }

            $IG_USERNAME = $this->getUserIGAccountName($access_token);
            $longLivedTokenData = $this->getUserLongLivedToken($access_token);

            $permissions_CS_string = implode(',', $permissions);

            $updated_IGAccessCode = $current_user->IGAccessCodes()->updateOrCreate(
                [
                    'user_id' => $current_user->id,
                    'IG_USERNAME' => $IG_USERNAME
                ],
                [
                    'IG_APP_SCOPED_ID' => $IG_APP_SCOPED_ID,
                    'short_lived_access_token' => $access_token,
                    'permissions' => $permissions_CS_string,
                    'long_lived_access_token' => $longLivedTokenData['access_token'] ?? '',
                    'long_lived_expires_in' => $longLivedTokenData['expires_in'] ?? '',
                ]
            );

            // WebhookController::subscribeToWebhook($updated_IGAccessCode);

            user_mongodb_subprofile::where("email", "=", $current_user->email)
                ->push(
                    'IG_bussiness_accounts',
                    [$IG_USERNAME],
                    unique: true
                );

            $ig_account_entry = $current_user->IGAccessCodes()
                ->where('user_id', '=', $current_user->id)
                ->where('IG_USERNAME', '=', $IG_USERNAME)->first();

            $this->syncData($ig_account_entry, $current_user);

            DB::commit();

            return redirect()->route('profile.edit');
        } catch (\Exception $th) {
            logger(print_r($th->getMessage(), true));

            DB::rollBack();

            return redirect()->action([ProfileController::class, 'AuthError']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            //code...
            $current_user = $request->user();
            $request_id = hash('md5', uniqid());

            $client_id = config('ig.IG_CLIENT_ID');
            $redirect_url = config('ig.IG_REDIRECT_URL');
            $scopes = config('ig.IG_SCOPES');
            $response_type = config('ig.IG_RESPONSE_TYPE');

            // IG_AUTH_URL = "https://www.instagram.com/oauth/authorize?client_id=453880354213839&redirect_uri=https://likeandshare.test/callback&response_type=code&scope=business_basic%2Cbusiness_manage_messages%2Cbusiness_manage_comments%2Cbusiness_content_publish"


            $current_user->authRequest()->create(
                [
                    "request_id" => $request_id
                ]
            );

            $IGOauthURL = "https://www.instagram.com/oauth/authorize?";
            $IGOauthURL = $IGOauthURL . "client_id=" . $client_id . "&";
            $IGOauthURL = $IGOauthURL . "redirect_uri=" . $redirect_url . "&";
            $IGOauthURL = $IGOauthURL . "response_type=" . $response_type . "&";
            $IGOauthURL = $IGOauthURL . "scope=" . $scopes . "&";
            $IGOauthURL = $IGOauthURL . "state=" . $request_id;

            if (!$IGOauthURL) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'general' => 'unable to process your request. Please try again'
                ]);
            }

            // logger(print_r($IGOauthURL, true));
            return Inertia::location($IGOauthURL);
        } catch (\Throwable $th) {
            //throw $th;
            logger(print_r($th->getMessage(), true));
        }
    }


    // public function setupWebhook(Request $request)
    // {
    //     try {
    //         //code...
    //         $current_user = $request->user();
    //         //code...
    //         $validated = $request->validate([
    //             'IG_APP_SCOPED_ID' => 'required|numeric',
    //             'IG_USERNAME' => 'required|string',
    //         ]);

    //         $IGAccessCode = IGAccessCodes::where('IG_APP_SCOPED_ID', '=', $validated['IG_APP_SCOPED_ID'])
    //             ->where('IG_USERNAME', '=', $validated['IG_USERNAME'])
    //             ->first() ?? null;


    //         $result = WebhookController::subscribeToWebhook($IGAccessCode);

    //         if (!$result) {
    //             throw new Error('subscribeToWebhook Process Failed');
    //         }
    //     } catch (\Throwable $th) {
    //         logger("setupWebhook Error" . $th->getMessage());

    //         return back()->withErrors(['general_error_status' => 'error'])->withInput();
    //     }
    // }
}
