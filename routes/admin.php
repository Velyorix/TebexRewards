<?php

use Azuriom\Plugin\Tebexrewards\Controllers\Admin\AdminController;
use Azuriom\Plugin\Tebexrewards\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your plugin. These
| routes are loaded by the RouteServiceProvider of your plugin within
| a group which contains the "web" middleware group and your plugin name
| as prefix. Now create something great!
|
*/

Route::get('/', [AdminController::class, 'index'])->name('index');

Route::get('/settings', [SettingsController::class, 'show'])->name('settings');
Route::post('/settings', [SettingsController::class, 'save'])->name('settings.save');

Route::get('/leaderboard', [AdminController::class, 'leaderboard'])->name('leaderboard');
Route::get('/progress', [AdminController::class, 'progress'])->name('progress');
Route::get('/ranks', [AdminController::class, 'ranks'])->name('ranks');
