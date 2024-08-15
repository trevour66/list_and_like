<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\NewConnectedIGBusinessAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\IGMedia;

use Illuminate\Queue\InteractsWithQueue;

class PullRecentData implements ShouldQueue
{
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
    public function handle(NewConnectedIGBusinessAccount $event): void
    {
        //
        logger(print_r("handle: NewConnectedIGBusinessAccount", true));

        $IGAccountUnder = $event->IG_access_code;
        $user = $event->IG_access_code->user;

        $IG_MediaService = new IGMedia($IGAccountUnder, $user);
        $IG_MediaService->pullRecentUserPost();
    }
}
