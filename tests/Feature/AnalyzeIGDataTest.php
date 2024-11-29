<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Dashboard_Analytics\EngagementAnalyzerService;


class AnalyzeIGDataTest extends TestCase
{

    public function test_analyze_IG_data()
    {
        $response = new EngagementAnalyzerService();

        $response->prepareEngagements();
    }
}
