<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;

use App\Ride;
use App\User;
use App\Request;
use Illuminate\Http\Request as WebRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RidesController extends Controller
{
    public function __construct()
    {
        $this->content = array();
    }
    public function index()
    {
        $user = User::findOrFail(request('userId'));
        $rides = $user->rides;
        foreach ($rides as $ride){
            if($ride->requests){
                $ride['requests'] = $ride->requests;
            }
        }
        $this->content['rides'] = $rides;
        return response()->json($this->content);
     }


     public function destroy()
     {
         $ride = Ride::findOrFail(request('rideId'));
         if ($ride!=null) {
             $ride->delete();
             $this->content['status'] = 'done';
             return response()->json($this->content);
         }else{
            $this->content['status'] = 'already deleted';
            return response()->json($this->content);
         }


     }

public static function x(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo
    ) {
        $long1 = deg2rad($longitudeFrom);
        $long2 = deg2rad($longitudeTo);
        $lat1 = deg2rad($latitudeFrom);
        $lat2 = deg2rad($latitudeTo);

        $dlong = $long2 - $long1;
        $dlati = $lat2 - $lat1;

        $val = pow(sin($dlati/2), 2)+cos($lat1)*cos($lat2)*pow(sin($dlong/2), 2);

        $res = 2 * asin(sqrt($val));

        $radius = 3958.756;

        return ($res*$radius);
    }


    public function viewAvailableRides(Request $request)
    {
        $request = Request::findOrFail(request('id'));
        if ($request->response == false) {
            $rides = Ride::all()
            ->where('user_id', '<>', $request->user_id)
            ->where('time', '>=', $request->time)
            ->where('availableSeats', '>=', $request->neededSeats)
            ->where('available', true);
            $filtered = $rides->filter(function ($value, $key) use ($request) {          
return (self::x(
                    $request->destinationLatitude,
                    $request->destinationLongitude,
                    $value->destinationLatitude,
                    $value->destinationLongitude
                )<5);
            });

            $this->content['rides'] = $filtered->values();
            return response()->json($this->content);
        } else {
            $this->content['rides'] = $request->ride;
            return response()->json($this->content);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WebRequest $request)
    {
        $data = request()->all();
        $rules = [
            'startPointLatitude' => ['required'],
            'startPointLongitude' => ['required'],
            'endPointLatitude' => ['required'],
            'endPointLongitude' => ['required'],
            'availableSeats' => ['required'],
            'time' => ['required'],
            'userId' => ['required']

        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            //dd(request('startPointLatitude'));
            Ride::create([
                'startPointLatitude' =>request('startPointLatitude'),
                'startPointLongitude' =>request('startPointLongitude'),
                'destinationLatitude' =>request('endPointLatitude'),
                'destinationLongitude' =>request('endPointLongitude'),
                'availableSeats' =>request('availableSeats'),
                'time' => request('time'),
                'available' => true,
                'user_id' => request('userId')
            ]);
            $this->content['status'] = 'done';
            return response()->json($this->content);
        } else {
            $this->content['status'] = 'undone';
            $this->content['details'] = $validator->errors()->all();
            return response()->json($this->content);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ride  $ride
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $data = request()->all();
        $rules = [
            'startPointLatitude' => ['required'],
            'startPointLongitude' => ['required'],
            'endPointLatitude' => ['required'],
            'endPointLongitude' => ['required'],
            'availableSeats' => ['required'],
            'time' => ['required'],
            'userId' => ['required']
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $ride=Ride::find(request('rideId'));
            $ride->update([
                'startPointLatitude' =>request('startPointLatitude'),
                'startPointLongitude' =>request('startPointLongitude'),
                'destinationLatitude' =>request('endPointLatitude'),
                'destinationLongitude' =>request('endPointLongitude'),
                'availableSeats' =>request('availableSeats'),
                'time' => request('time'),
                'available' => true,
                'user_id' => request('userId')
            ]);
            $this->content['status'] = 'done';
            return response()->json($this->content);
    } else {
            $this->content['status'] = 'undone';
            $this->content['details'] = $validator->errors()->all();
            return response()->json($this->content);
    }





    }

    public function viewSentRequests($id)
    {
        $ride = Ride::find($id);
        $requestts = Ride::find($id)->requestts->where('neeededSeats', '<=', $ride->availableSeats)->where('response', false);
        return view('rides.viewSentRequests')->with('requestts', $requestts)->with('ride', $ride);
    }

    public function acceptRequest()
    {
        $requestt = Request::find(request('requestId'));
        $ride = Ride::find(request('rideId'));
        if ($ride->availableSeats >= $requestt->neededSeats && $requestt->response == false) {
            $requestt->update([
                'response' => true,
                'ride_id' => $ride->id,
            ]);
            $ride->update([
                'availableSeats' => $ride->availableSeats - $requestt->neededSeats,
            ]);
            $this->content['status'] = 'done';
            return response()->json($this->content);

        } else {
            $this->content['status'] = 'unAvailable';
            return response()->json($this->content);

        }

    }
}
