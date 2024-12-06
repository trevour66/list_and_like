<?php

namespace App\Listeners;

use App\Events\NewDataFetchRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\IGMedia;

use App\Models\ig_data_fetch_process;
use Exception;
use Illuminate\Queue\InteractsWithQueue;

use App\Notifications\NewDataFetch;
use App\Notifications\SuccessfulDataFetch;
use App\Notifications\FailedDataFetch;

class DataFetchRequest_PullRecentData implements ShouldQueue
{
    use InteractsWithQueue;


    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public $delay = 5;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewDataFetchRequest $event): void
    {
        //
        logger(print_r("handle: NewDataFetchRequest", true));
        // logger(print_r($event->ig_data_fetch_process, true));

        $ig_data_fetch_process_id = $event->ig_data_fetch_process->id;

        try {

            $IGAccountUnder = $event->ig_data_fetch_process->ig_access_code;
            // logger($event->ig_data_fetch_process);
            // logger($IGAccountUnder);
            // return;
            $user = $event->ig_data_fetch_process->ig_access_code->user;

            try {
                $user->notify(new NewDataFetch($IGAccountUnder));
            } catch (\Throwable $th) {
                logger(print_r("handle: NewDataFetchRequest NEWDATAFETCH Notify Error:", true));
                logger(print_r($th->getMessage(), true));
            }

            $IG_MediaService = new IGMedia($IGAccountUnder, $user);
            $IG_MediaService->pullRecentUserPost();

            ig_data_fetch_process::where('id', '=', $ig_data_fetch_process_id)
                ->update(
                    ['IDFP_status' => 'finished_success'],
                );


            try {
                $user->notify(new SuccessfulDataFetch($IGAccountUnder));
            } catch (\Throwable $th) {
                logger(print_r("handle: NewDataFetchRequest SUCCESSFUL FETCH Notify Error:", true));
                logger(print_r($th->getMessage(), true));
            }
        } catch (Exception $e) {
            logger(print_r("handle: NewDataFetchRequest Error:", true));
            logger(print_r($e->getMessage(), true));

            ig_data_fetch_process::where('id', '=', $ig_data_fetch_process_id)
                ->update(
                    ['IDFP_status' => 'finished_error'],
                );


            try {
                $user->notify(new FailedDataFetch($IGAccountUnder));
            } catch (\Throwable $th) {
                logger(print_r("handle: NewDataFetchRequest FailedDataFetch Notify Error:", true));
                logger(print_r($th->getMessage(), true));
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(NewDataFetchRequest $event, \Throwable $exception): void
    {
        // ...

        logger(print_r("handle: NewDataFetchRequest Error:", true));
        logger(print_r($exception->getMessage(), true));

        $ig_data_fetch_process_id = $event->ig_data_fetch_process->id;


        ig_data_fetch_process::where('id', '=', $ig_data_fetch_process_id)
            ->update(
                ['IDFP_status' => 'finished_error'],
            );

        return;
    }
}
