<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Jobs\ScrapeInstagramProfiles;
use Illuminate\Support\Facades\Bus;

class ScrapeInstagramProfilesTest_ extends TestCase
{
    /** @test */
    public function it_dispatches_test_job()
    {
        // Fake the job dispatching
        Bus::fake();

        // Dispatch the job
        ScrapeInstagramProfiles::dispatch();

        // Assert the job was dispatched
        Bus::assertDispatched(ScrapeInstagramProfiles::class);
    }

    /** @test */
    public function test_job_runs()
    {
        // Fake the job dispatching
        Bus::fake();

        // Dispatch the job
        ScrapeInstagramProfiles::dispatch();

        // Assert the job was dispatched
        Bus::assertDispatched(ScrapeInstagramProfiles::class, function ($job) {
            $job->handle();
            return true;
        });
    }
}
