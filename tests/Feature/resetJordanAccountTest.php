<?php

namespace Tests\Feature;

use Tests\TestCase;

use App\Models\user_mongodb_subprofile;
use App\Models\ig_business_account_posts;


class resetJordanAccountTest extends TestCase
{

    public function test_reset_profile()
    {
        logger("started");

        $jordan_user_mongodb_subprofile = user_mongodb_subprofile::where("email", "=", "jordanhgill@gmail.com")->first();

        // logger(print_r($jordan_user_mongodb_subprofile, true));
        logger(print_r($jordan_user_mongodb_subprofile->userlists()->delete(), true));

        $posts = ig_business_account_posts::where("ig_business_account", "=", "systemssavedme")->get() ?? [];

        foreach ($posts as $key => $value) {
            logger(print_r($value->comments()->delete(), true));
            logger(print_r($value->delete(), true));
        }
    }
}
