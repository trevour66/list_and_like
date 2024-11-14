<?php

namespace App\Http\Controllers;

use App\Dashboard_Analytics\EngagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserEngagementsController extends Controller
{
    /**
     * Get the IG profile with the highest engagement for a business account.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function engagement_data(Request $request): JsonResponse
    {
        try {

            $businessAccountId = $request->input('business_account_id');

            $engagementService = new EngagementService($businessAccountId);

            $engagementService->prepareEngagementProfile();

            return response()->json([
                'status' => "success",
                'data' => [
                    'highest_engagement_profile' => $engagementService->getHighestEngaged(),
                    'lowest_engagement_profile' => $engagementService->getLowestEngaged(),
                    'all_data' => $engagementService->getAllData()
                ]
            ]);
        } catch (\Throwable $th) {
            logger("engagementData API Error" . $th->getMessage());
            $resData = response(json_encode(
                [
                    'status' => "error",
                    "data" => null
                ]
            ), 200)
                ->header('Content-Type', 'application/json');

            return $resData;
        }
    }
}
