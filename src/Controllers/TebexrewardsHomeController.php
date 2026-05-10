<?php

namespace Azuriom\Plugin\Tebexrewards\Controllers;

use Azuriom\Http\Controllers\Controller;

class TebexrewardsHomeController extends Controller
{
    /**
     * Show the home plugin page.
     */
    public function index()
    {
        return view('tebexrewards::index');
    }
}
