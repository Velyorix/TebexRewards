<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Show the home admin page of the plugin.
     */
    public function index()
    {
        return redirect()->route('tebexrewards.admin.settings');
    }

    public function leaderboard()
    {
        return view('tebexrewards::admin.leaderboard');
    }

    public function progress()
    {
        return view('tebexrewards::admin.progress');
    }

    public function ranks()
    {
        return view('tebexrewards::admin.ranks');
    }
}
