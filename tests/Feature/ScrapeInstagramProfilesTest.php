<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Events\NewConnectedIGBusinessAccount;
use App\Listeners\PullRecentData;
use App\Models\IGAccessCodes;
use App\Services\ApifyScraper;

// php artisan test --filter=PullRecentUserDataListenerTest

class ScrapeInstagramProfilesTest extends TestCase
{

    public function test_scrape_ig_profile()
    {
        logger("started");
        $scrapper = new ApifyScraper();
        $scrapper->scrape();
    }
}
