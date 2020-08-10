<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        config([
            'MAIL_USERNAME' => 'khairtoss2212@gmail.com',
            'MAIL_PASSWORD' =>'toto2212',
            'MAIL_HOST' =>'smtp.gmail.com',
            'MAIL_PORT' =>'587',
            'MAIL_ENCRYPTION' =>'tls',
            'MAIL_MAILER' =>'smtp',
            'MAIL_FROM_ADDRESS'=>'khairtoss2212@gmail.com'
        ]);
    //dd(config('MAIL_FROM_ADDRESS'));
        
        return view('home');
    }
}
