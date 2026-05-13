<?php

use Azuriom\Plugin\Tebexrewards\Controllers\Api\ApiController;
use Azuriom\Plugin\Tebexrewards\Controllers\Api\WidgetsController;
use Azuriom\Plugin\Tebexrewards\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', [ApiController::class, 'index']);

Route::post('/webhook', [WebhookController::class, 'handle'])->name('webhook');

Route::get('/widgets/last-purchaser', [WidgetsController::class, 'lastPurchaser'])->name('widgets.last_purchaser');

Route::get('/widgets/leaderboard', [WidgetsController::class, 'leaderboard'])->name('widgets.leaderboard');
