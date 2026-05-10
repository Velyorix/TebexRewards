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
        return view('tebexrewards::admin.index');
    }
}
