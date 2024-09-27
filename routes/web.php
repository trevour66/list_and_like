<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IGBusinessLoginController;
use App\Http\Controllers\CommunityController;

use App\Http\Controllers\IgProfilesController;
use App\Http\Controllers\UserListController;
use App\Http\Controllers\IgProfilePostController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\DashboardController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/sync-data', [DashboardController::class, 'sync_data'])->name('dashboard.sync_data');

    Route::get('/callback', [IGBusinessLoginController::class, 'index'])->name('callback.index');
    Route::post('/new-ig-connection', [IGBusinessLoginController::class, 'store'])->name('authRequest.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/added-ig-profiles', [IgProfilesController::class, 'index'])->name('added_ig_profile.index');

    Route::get('/my-lists', [UserListController::class, 'index'])->name('user_lists.index');
    Route::get('/my-lists/{userList}', [UserListController::class, 'show'])->name('user_lists.show');
    Route::post('/my-lists', [UserListController::class, 'create'])->name('user_lists.create');
    Route::post('/my-lists/{userList}/add-profile', [UserListController::class, 'store'])->name('user_lists.store_profile');
    Route::post('/my-lists/{userList}/delete', [UserListController::class, 'destroy'])->name('user_lists.destroy_profile');

    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');

    Route::post('/ig-post/skip/{post_id}', [IgProfilePostController::class, 'skip'])->name('ig_profile_post.skip');
    Route::post('/ig-post/react/{post_id}', [IgProfilePostController::class, 'react'])->name('ig_profile_post.react');
});

require __DIR__ . '/auth.php';
