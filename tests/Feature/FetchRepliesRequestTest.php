<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class FetchRepliesRequestTest extends TestCase
{

    public function test_reolies_fetching_request()
    {
        $parent_comment_id = "17952870221851410";
        $post_id = "673f031454691590ad032792";
        $from = "retty_tech";



        $response = $this->postJson('/api/get-all-replies', [
            'post_id' => $post_id,
            'from' => $from,
            'parent_comment_id' => $parent_comment_id
        ]);

        // logger(print_r($response, true));

        $response->assertStatus(200);
    }
}
