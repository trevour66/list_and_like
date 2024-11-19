<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CalculateDashBoardDataFetchRequestTest extends TestCase
{

    public function test_calculate_dashboard_data_fetch_request()
    {
        // $IG_account_id = 1;
        $IG_username = "systemssavedme";

        $response = $this->postJson('/get-analytics', [
            // 'IG_account_id' => $IG_account_id,
            'IG_username' => $IG_username,
        ]);

        // logger(print_r($response, true));

        $response->assertStatus(200);
    }
}
