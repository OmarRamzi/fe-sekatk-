<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Car;
use App\Profile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->content = array();
    }
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();

            $this->content['user'] = $user;
            $this->content['user']['profile'] = $user->profile;
            $this->content['user']['car'] = $user->car;
            return response()->json($this->content);
        } else {
            $this->content['error'] = "Unauthorized";
            return response()->json($this->content);
        }
    }
    public function getById()
    {
        $user=User::find(request('userId'));
        $user['profile']=$user->profile;
        return $user;
    }
    public function register()
    {
        $data = request()->all();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phoneNumber' => ['required', 'string', 'min:8', 'unique:users'],
            'nationalId' => ['required', 'string', 'min:8', 'unique:users']
        ];
        $carRules = [
            'license' => ['required','min:8','unique:cars'],
            'model' => 'required|string',
            'color' => 'required|string',
            'userLicense' => 'required|min:8|unique:cars',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = new User;
            $user->name = request('name');
            $user->email = request('email');
            $user->phoneNumber = request('phoneNumber');
            $user->nationalId = request('nationalId');
            $user->password = Hash::make(request('password'));
//            dd(request('car')['license']);
            if (request('car') != null) {
                $validator = Validator::make(request('car'), $carRules);
                if ($validator->passes()) {
                    $car = new Car;
                    $car->license = request('car')['license'];
                    $car->carModel = request('car')['model'];
                    $car->color = request('car')['color'];
                    $car->userLicense = request('car')['userLicense'];
                    $user->save();
                    $car->user_id = $user->id;
                    $car->save();
                    $profile = Profile::create([
                        'user_id' => $user->id,
                        'picture' => $user->getGravatar(),
                    ]);
                    $this->content['status'] = 'done';
                    return response()->json($this->content);
                } else {
                    $this->content['status'] = 'undone';
                    $this->content['details'] = $validator->errors()->all();
                    return response()->json($this->content);
                }
            }
            $user->save();
            $profile = Profile::create([
                'user_id' => $user->id,
                'picture' => $user->getGravatar(),
            ]);
            $this->content['status'] = 'done';
        } else {
            $this->content['status'] = 'undone';
            $this->content['details'] = $validator->errors()->all();
        }
        return response()->json($this->content);
    }
    public function details()
    {
        return response()->json(['user' => Auth::user()]);
    }







    public function destroy()
    {
        $user = User::find(request('userId'));
        if($user!=null){
            $user->delete();
            $this->content['status'] = 'done';
            return response()->json($this->content);
        }else{
           $this->content['status'] = 'already deleted';
           return response()->json($this->content);
        }

    }





    //new function edit user:
    public function edit()
    {
        $data = request()->all();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phoneNumber' => ['required', 'string', 'min:8'],
            'password' => ['required', 'string', 'min:8'],
            'job'=>['string']

        ];
        $carRules = [
            'car'=>[
                'license' => 'min:8',
                'model' => 'string',
                'color' => 'string',
                'userLicense' => 'min:8',
            ]



        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $user = User::findOrFail(request('user_id'));

            if (request('car') != null) {
                $validator = Validator::make(request('car'), $carRules);
                if ($validator->passes()) {
                    $car=$user->car;
                    if($car!=null){

                        $car->update([
                            'license' => request('car')['license'],
                            'carModel' => request('car')['model'],
                            'color' => request('car')['color'],
                            'userLicense' => request('car')['userLicense'],
                            //'user_id' => $user->id,
                        ]);

                    }else{

                        $car = $user->car()->create([
                            'license' => request('car')['license'],
                            'carModel' => request('car')['model'],
                            'color' => request('car')['color'],
                            'userLicense' => request('car')['userLicense'],

                        ]);


                    }



                } else {
                    $this->content['status'] = 'undone';
                    $this->content['details'] = $validator->errors()->all();
                    return response()->json($this->content);
                }
            }
            $user->update([
                'name'=>request('name'),
                'mobileNum'=>request('phoneNumber'),
                'password'=>request('password'),
            ]);
            $profile=$user->profile;
            $profile->update([
                'job' => request('job'),
            ]);
            if (request()->hasFile('picture')) {
                $picture = request('picture')->store('profilesPictures', 'public');
                $profile->update([
                'picture'=>$picture,
                ]);
            }
            $this->content['status'] = 'done';
            return response()->json($this->content);
        } else {
            $this->content['status'] = 'undone';
            $this->content['details'] = $validator->errors()->all();
            return response()->json($this->content);
        }
    }

}
