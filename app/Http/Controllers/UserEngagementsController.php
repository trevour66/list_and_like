<?php

namespace App\Http\Controllers;

use App\Dashboard_Analytics\EngagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

use App\Models\user_mongodb_subprofile;
use App\Models\user_list;


class UserEngagementsController extends Controller
{
    public function top_five(Request $request)
    {
        try {
            $businessAccountId = $request->input('business_account_id');

            $engagementService = new EngagementService($businessAccountId);

            return response()->json([
                'status' => "success",
                'data' => $engagementService->getTopFiveEngagers()
            ]);
        } catch (\Throwable $th) {
            logger("UserEngagementsController API Error " . $th->getMessage());
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

    public function others(Request $request)
    {
        try {
            $businessAccountId = $request->input('business_account_id');

            $engagementService = new EngagementService($businessAccountId);

            return response()->json([
                'status' => "success",
                'data' => $engagementService->getOtherEngagers()
            ]);
        } catch (\Throwable $th) {
            logger("UserEngagementsController API Error " . $th->getMessage());
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
    public function index(Request $request)
    {
        $email = $request->user()->email;
        $user = $request->user();

        $user_mongodb = user_mongodb_subprofile::where('email', '=', $email)->first() ?? false;

        $user_lists = [];

        if ($user_mongodb) {
            $user_lists = user_list::where('user_id', '=', $user_mongodb->user_id)->get() ?? [];
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

        // logger($user_lists);
        return Inertia::render('Engagement/Engagement', [
            'user_lists' => $user_lists,
            "ig_data_fetch_process" => $igDataFetchProcess
        ]);
    }
}
