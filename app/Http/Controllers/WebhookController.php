<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function verify(Request $request)
    {
        // Define the verification token (you can set it in the .env file)
        $token = env('WEBHOOK_VERIFY_TOKEN');

        // Check the query parameters to verify the webhook
        if (
            $request->query('hub_mode') === 'subscribe' &&
            $request->query('hub_verify_token') === $token
        ) {
            return response($request->query('hub_challenge'));
        }

        return response()->json(['error' => 'Unauthorized'], 400);
    }
}
