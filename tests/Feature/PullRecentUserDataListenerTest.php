<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Events\NewConnectedIGBusinessAccount;
use App\Listeners\PullRecentData;
use App\Models\IGAccessCodes;
// php artisan test --filter=PullRecentUserDataListenerTest

class PullRecentUserDataListenerTest extends TestCase
{

    public function test_pull_recent_user_data_listener()
    {
        // Create a user
        $ig_access_code_added = IGAccessCodes::factory()->create();

        // Fire the event
        event(new NewConnectedIGBusinessAccount($ig_access_code_added));
    }
}
