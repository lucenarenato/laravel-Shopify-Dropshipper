<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Show Help Topics page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $store = \Auth::user()->name;
        return view('help', compact('store'));
    }

    /**
     * Show Tutorials page
     *
     * @return \Illuminate\Http\Response
     */
    public function tutorials()
    {
        return view('tutorials');
    }

    /**
     * Show Contact page
     *
     * @return \Illuminate\Http\Response
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Show Announcements page
     *
     * @return \Illuminate\Http\Response
     */
    public function announcements()
    {
        $alertsContent = file_get_contents('https://' . $_SERVER ['HTTP_HOST'] . '/js/alerts.json');
        $alerts = array_reverse(json_decode($alertsContent, true));
        setcookie("ad_last_read_alert", $alerts[0]['id'], 2147483647);
        return view('announcements')->with('alerts', $alerts);
    }
}
