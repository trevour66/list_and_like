<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ig_data_fetch_process;
use App\Services\IGMedia;


class ProcessDataFetchRequest_PullRecentData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ig_data_fetch_process $ig_data_fetch_process
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger(print_r("handle: ProcessDataFetchRequest_PullRecentData", true));

        $ig_data_fetch_process = $this->ig_data_fetch_process;
        logger(print_r($ig_data_fetch_process, true));
        return;

        $ig_data_fetch_process_id = $ig_data_fetch_process->id;

        try {

            $IGAccountUnder = $ig_data_fetch_process->ig_access_code;
            $user = $ig_data_fetch_process->ig_access_code->user;

            $IG_MediaService = new IGMedia($IGAccountUnder, $user);
            $IG_MediaService->pullRecentUserPost();

            ig_data_fetch_process::where('id', '=', $ig_data_fetch_process_id)
                ->update(
                    ['IDFP_status' => 'finished_success'],
                );

            return;
        } catch (\Exception $e) {
            logger(print_r("handle: NewDataFetchRequest Error:", true));
            logger(print_r($e->getMessage(), true));

            ig_data_fetch_process::where('id', '=', $ig_data_fetch_process_id)
                ->update(
                    ['IDFP_status' => 'finished_error'],
                );

            return;
        }
    }
}
