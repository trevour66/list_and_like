<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\API_UserAuth;
use App\Http\Controllers\IgProfilesController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserEngagementsController;

use App\Http\Controllers\IgBusinessAccountPostsController;
use App\Http\Controllers\IgBusinessAccountPostCommentsController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth')->group(function () {
    // In-APP API using SESSION Cookie
    Route::get('/get-added-ig-profiles', [IgProfilesController::class, 'index_api'])->name('added_ig_profile.index_api');

    Route::post('/get-community-dashboard', [DashboardController::class, 'fetch_community_data'])->name('dashboard.fetch_community_data');
    Route::post('/get-analytics', [DashboardController::class, 'fetch_account_analytics_data'])->name('dashboard.fetch_account_analytics_data');

    Route::post('/my-posts', [IgBusinessAccountPostsController::class, 'index_api'])->name('my_post.index_api');
    Route::post('/get-post-comments', [IgBusinessAccountPostCommentsController::class, 'get_comments_api'])->name('my_post.get_comments_api');
    Route::post('/reply-comment', [IgBusinessAccountPostCommentsController::class, 'reply_to_comment_api'])->name('my_post.reply_to_comment_api');
    Route::post('/new-comment', [IgBusinessAccountPostCommentsController::class, 'new_comment_api'])->name('my_post.new_comment_api');

    Route::get('/community', [CommunityController::class, 'index_api'])->name('community.index_api');
    Route::post('/get-all-replies', [IgBusinessAccountPostCommentsController::class, 'get_all_comment_replies_api'])->name('my_post.get_all_comment_replies_api');
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/add-ig-profile', [IgProfilesController::class, 'chrome_extension_add_ig_username'])->name('actionStepCore.chrome_extension_add_ig_username');
});


Route::post('/login', [API_UserAuth::class, 'authenticateUser'])->name('apiUserAuth.authenticateUser');
