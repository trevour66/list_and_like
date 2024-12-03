<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Dashboard_Analytics\EngagementAnalyzerService;


class AnalyzeIGData implements ShouldBeUnique
{
    // use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Dispatchable,  SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger("started EngagementAnalyzerService");
        $response = new EngagementAnalyzerService();

        $response->prepareEngagements();
    }
}
