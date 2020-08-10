<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\Profile;
use App\Car;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phoneNumber' => ['required', 'string', 'min:8', 'unique:users'],
            'nationalId' => ['required', 'string', 'min:8', 'unique:users'],

        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
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
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phoneNumber' => $data['phoneNumber'],
            'nationalId' => $data['nationalId'],
            'password' => Hash::make($data['password']),
        ]);
        $profile = Profile::create([
            'user_id' => $user->id,
            'picture'=>$user->getGravatar(),
        ]);
       // if ($user->type == 'driver') {
            /*$car = Car::create([
                'user_id' => $user->id,
            ]);*/
       // }
        return $user;
    }
    protected function redirectTo()
    {
        return'/home';
    }

}
