<?php

use Azuriom\Plugin\Tebexrewards\Controllers\Admin\AdminController;
use Azuriom\Plugin\Tebexrewards\Controllers\Admin\RankTierController;
use Azuriom\Plugin\Tebexrewards\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register your plugin admin routes. These routes
| are loaded by the RouteServiceProvider within a group which contains
| the "admin-access" middleware group.
|
*/

Route::get('/', [AdminController::class, 'index'])->name('index');

Route::get('/settings', [SettingsController::class, 'show'])->name('settings');
Route::post('/settings', [SettingsController::class, 'save'])->name('settings.save');
Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear_cache');
Route::post('/settings/sync', [SettingsController::class, 'syncNow'])->name('settings.sync');

Route::get('/leaderboard', [AdminController::class, 'leaderboard'])->name('leaderboard');
Route::get('/progress', [AdminController::class, 'progress'])->name('progress');

Route::get('/ranks', [RankTierController::class, 'index'])->name('ranks');
Route::get('/ranks/{rankTier}/edit', [RankTierController::class, 'edit'])->name('ranks.edit');
Route::post('/ranks', [RankTierController::class, 'store'])->name('ranks.store');
Route::put('/ranks/{rankTier}', [RankTierController::class, 'update'])->name('ranks.update');
Route::delete('/ranks/{rankTier}', [RankTierController::class, 'destroy'])->name('ranks.destroy');
Route::post('/ranks/reset-defaults', [RankTierController::class, 'resetDefaults'])->name('ranks.reset');
Route::post('/ranks/profile-setting', [RankTierController::class, 'saveProfileSetting'])->name('ranks.profile_setting');
