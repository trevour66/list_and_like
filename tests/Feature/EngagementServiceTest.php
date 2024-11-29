<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Jobs\ScrapeInstagramProfiles;
use Illuminate\Support\Facades\Bus;
use App\Dashboard_Analytics\EngagementService;

class EngagementServiceTest extends TestCase
{
    /** @test */
    public function test_get_engagement()
    {

        // $IG_username = ["retty_tech", "systemssavedme"];
        $IG_username = ["systemssavedme", "retty_tech"];

        foreach ($IG_username as $key => $value) {

            $response = new EngagementService($value);

            // logger($response->getHighestEngagers());
            // logger($response->getLowestEngagers());
            // logger($response->getTopFiveEngagers());

            logger($response->getOtherEngagers(true));
            // logger($response->getTopFiveEngagers(true));

            break;
        }
    }
}
