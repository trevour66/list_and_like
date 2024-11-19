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

        $IG_username = "systemssavedme";

        $response = new EngagementService($IG_username);

        $response->prepareEngagementProfile();

        logger($response->getHighestEngaged());
        logger($response->getLowestEngaged());
        // logger($response->getAllData());
    }
}
