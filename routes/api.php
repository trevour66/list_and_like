<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\API_UserAuth;
use App\Http\Controllers\IgProfilesController;
use App\Http\Controllers\CommunityController;


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


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/add-ig-profile', [IgProfilesController::class, 'chrome_extension_add_ig_username'])->name('actionStepCore.chrome_extension_add_ig_username');
    Route::get('/community', [CommunityController::class, 'index_api'])->name('community.index_api');
});

Route::middleware('auth')->group(function () {
});

Route::post('/login', [API_UserAuth::class, 'authenticateUser'])->name('apiUserAuth.authenticateUser');
