<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservationResource;
use App\Http\Traits\MobileResponse;
use App\Models\Reservation;
use App\Models\Doctor;
use App\Models\NClinic;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    //
    use MobileResponse;

    public function update(Request $request,$id)
    {
        $reservation = Reservation::find($id);
        if(!$reservation){
            return $this ->fail("not Found", 404);
        }


        $reservation->update([
            
            'time' => $request->time,
            'date' => $request->date,
            'email' => $request->email,
            'duration' => $request->duration,

        ]);
        return $this->success( new ReservationResource($reservation) );
    }

    public function delete($id)
    {
        $reservation = Reservation::find($id);
        if($reservation){
            $reservation->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found");
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'time'=>'required',
            'date'=>'required',
            'email'=>'required',
            'duration'=>'required',
            'patient_id'=>'required|exists:patients,id',
            'doctor_id'=>'required|exists:doctors,id',
            'nclinic_id'=>'required|exists:nclinics,id',
            'user_id'=>'required|exists:users,id',
        ]);

        if($validator->fails()){

            return $this->fail($validator->errors()->first());

        }

        $reservation = Reservation::create([
            'time'=>$request->time,
            'date'=>$request->date,
            'email'=>$request->email,
            'duration'=>$request->duration,
            'doctor_id'=>$request->doctor_id,
            'patient_id'=>$request->patient_id,
            'nclinic_id'=>$request->nclinic_id,
            'user_id'=>$request->user_id,
            
        ]);
        return $this->success( new ReservationResource($reservation) );
    }

}

/*
    <?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservationResource;
use App\Http\Traits\MobileResponse;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    use MobileResponse;

    public function update(Request $request, $id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return $this->fail("Not Found", 404);
        }

        $validator = Validator::make($request->all(), [
            'time' => 'sometimes|required',
            'date' => 'sometimes|required',
            'email' => 'sometimes|required|email',
            'duration' => 'sometimes|required|integer'
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first(), 400);
        }

        $reservation->update($request->only(['time', 'date', 'email', 'duration']));
        return $this->success(new ReservationResource($reservation));
    }

    public function delete($id)
    {
        $reservation = Reservation::find($id);
        if ($reservation) {
            $reservation->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found", 404);
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time' => 'required',
            'date' => 'required',
            'email' => 'required|email',
            'duration' => 'required|integer',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'nclinic_id' => 'required|exists:nclinics,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first(), 400);
        }

        $reservation = Reservation::create($request->only(['time', 'date', 'email', 'duration', 'doctor_id', 'patient_id', 'nclinic_id', 'user_id']));
        return $this->success(new ReservationResource($reservation));
    }
}

*/