<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Error;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;


class API_UserAuth extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticateUser(Request $request)
    {
        try {
            $email = $request->input('email') ?? false;
            $password = $request->input('password') ?? false;

            if (
                !$email ||
                !$password
            ) {
                throw new Error('Login details incomplete');
            }

            $user = User::where('email', $email)->first() ?? false;

            if (!$user) {
                throw new Error('User not found');
            }

            $token = null;

            if (Hash::check($password, $user->password)) {

                $user->tokens()->where('name', 'dataAccess')->delete();

                $token = $user->createToken('dataAccess');
            } else {
                throw new Error('Wrong Password');
            }

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

            $resData = response(json_encode(
                [
                    'status' => "success",
                    "token" => $token,
                    "message" => "success",
                    "ig_data_fetch_process" => $igDataFetchProcess

                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        } catch (\Exception $e) {
            logger("API_UserAuth Error" . $e->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "message" => $e->getMessage(),
                    "token" => null
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }
}
