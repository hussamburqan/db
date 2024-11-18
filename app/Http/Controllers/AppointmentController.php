<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentResource;
use App\Http\Traits\MobileResponse;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Major;
use App\Models\Patient;
use App\Models\NClinic;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Rules\ValidateTime;

class AppointmentController extends Controller
{
    use MobileResponse;

    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return $this->fail("Not Found", 404);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'time' => ['required', new ValidateTime],
            'description' => 'required|string',
            'status' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
            'nclinic_id' => 'required|integer|exists:nclinics,id',
            'patient_id' => 'required|integer|exists:patients,id',
            'major_id' => 'required|integer|exists:majors,id',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        $appointment->update($request->only([
            'date',
            'time',
            'description',
            'status',
            'user_id',
            'major_id',
            'nclinic_id',
            'patient_id',
        ]));

        return $this->success(new AppointmentResource($appointment));
    }

    public function delete($id)
    {
        $appointment = Appointment::find($id);
        if ($appointment) {
            $appointment->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found", 404);
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'time' => ['required', new ValidateTime],
            'description' => 'required|string',
            'status' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
            'nclinic_id' => 'required|integer|exists:nclinics,id',
            'patient_id' => 'required|integer|exists:patients,id',
            'major_id' => 'required|integer|exists:majors,id',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        $appointment = Appointment::create([
            'date' => $request->date,
            'time' => $request->time,
            'description' => $request->description,
            'status' => $request->status,
            'user_id' => $request->user_id,
            'major_id' => $request->major_id,
            'nclinic_id' => $request->nclinic_id,
            'patient_id' => $request->patient_id,
        ]);

        return $this->success(new AppointmentResource($appointment));
    }

    public function all()
    {
        $appointments = Appointment::all();
        return $this->success(AppointmentResource::collection($appointments));
    }
}

/*
<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentResource;
use App\Http\Traits\MobileResponse;
use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function update(Request $request,$id)
    {
        $appointment = Appointment::find($id);
        if(!$appointment){
            return $this ->fail("not Found");
        }


        $appointment->update([
            'date' => $request->date,
            'time' => $request->time,
            'description' => $request->description,
            'status' => $request->status,
        ]);
        return $this->success( new AppointmentResource($appointment) );
    }

    public function delete($id)
    {
        $appointment = Appointment::find($id);
        if($appointment){
            $appointment->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found");
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'date'=>'required',
            'time'=>'required',
            'description'=>'required',
            'status'=>'required',
            'user_id'=>'required|exists:users,id',
            'nclinic_id'=>'required|exists:nclinics,id',
            'patient_id'=>'required|exists:patients,id',
            'major_id'=>'required|exists:majors,id',
        ]);

        if($validator->fails()){

            return $this->fail($validator->errors()->first());

        }

        $appointment = Appointment::create([
            'date'=>$request->date,
            'time'=>$request->time,
            'description'=>$request->description,
            'status'=>$request->status,
            'user_id'=>$request->user_id,
            'major_id'=>$request->major_id,
            'nclinic_id'=>$request->nclinic_id,
            'patient_id'=>$request->patient_id,
            
        ]);
        return $this->success( new AppointmentResource($appointment) );
    }

    public function all()
    {
        $appointments = Appointment::all();
        return $this->success( AppointmentResource::collection($appointment) );
    }
}
*/