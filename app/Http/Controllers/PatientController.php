<?php

namespace App\Http\Controllers;

use App\Http\Resources\PatientResource;
use App\Http\Traits\MobileResponse;
use App\Models\Patient;
use App\Models\User;
use App\Models\NClinic;
use App\Models\Doctor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    use MobileResponse;

    public function update(Request $request, $id)
    {
        $patient = Patient::find($id);
        if (!$patient) {
            return $this->fail("Not Found", 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:255|unique:patients,phone,' . $id,
            'address' => 'sometimes|required|string',
            'age' => 'sometimes|required|integer',
            'email' => 'sometimes|required|email|unique:patients,email,' . $id,
            'blood_type' => 'sometimes|required|string',
            'gender' => 'sometimes|required|string',
            'disease_type' => 'sometimes|required|string',
            'medical_history' => 'sometimes|required|string',
            'medical_recommendations' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        $patient->update(array_filter([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'age' => $request->age,
            'email' => $request->email,
            'blood_type' => $request->blood_type,
            'gender' => $request->gender,
            'disease_type' => $request->disease_type,
            'medical_history' => $request->medical_history,
            'medical_recommendations' => $request->medical_recommendations,
        ]));

        return $this->success(new PatientResource($patient));
    }

    public function delete($id)
    {
        $patient = Patient::find($id);
        if ($patient) {
            $patient->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found", 404);
        }
    }

    public function add(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'phone' => 'required|integer',
        'address' => 'required|string',
        'age' => 'required|integer',
        'email' => 'required|string|email|max:255',
        'blood_type' => 'required|string',
        'gender' => 'required|string',
        'disease_type' => 'required|string',
        'medical_history' => 'required|string',
        'medical_recommendations' => 'required|string'
    ]);

    if ($validator->fails()) {
        return $this->fail($validator->errors()->first());
    }

    $patient = Patient::create([
        'name' => $request->name,
        'phone' => $request->phone,
        'address' => $request->address,
        'age' => $request->age,
        'email' => $request->email,
        'blood_type' => $request->blood_type,
        'gender' => $request->gender,
        'disease_type' => $request->disease_type,
        'medical_history' => $request->medical_history,
        'medical_recommendations' => $request->medical_recommendations
    ]);

    return $this->success(new PatientResource($patient));
}
    public function all()
    {
        $patients = Patient::all();
        return $this->success(PatientResource::collection($patients));
    }
}




/*
<?php

namespace App\Http\Controllers;


use App\Http\Resources\PatientResource;
use App\Http\Traits\MobileResponse;
use App\Models\Patient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PatientController extends Controller
{

    use MobileResponse;


    public function update(Request $request,$id)
    {
        $patient = Patient::find($id);
        if(!$nclinic){
            return $this ->fail("not Found");
        }


        $patient->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'age' => $request->age,
            'email' => $request->email,
            'blood_type' => $request->blood_type,
            'gender' => $request->gender,
            'disease_type' => $request->disease_type,
            'medical_history' => $medical_history,
            'medical_recommendations' => $request->medical_recommendations,
        ]);
        return $this->success( new PatientResource($patient) );
    }

    public function delete($id)
    {
        $patient = Patient::find($id);
        if(!$patient){
            $patient->delete();
        }else{
            return $this->fail("not Found");
        }    
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'phone'=>'required|unique:users,phone',
            'address'=>'required',
            'age'=>'required',
            'email'=>'required|unique:users,email',
            'blood_type'=>'required',
            'gender'=>'required',
            'disease_type'=>'required',
            'medical_history'=>'required',
            'medical_recommendations'=>'required',
            'user_id'=>'required|exists:users,id',
            'doctor_id'=>'required|exists:doctors,id',
            'patient_id'=>'required|exists:patients,id',
            'major_id'=>'required|exists:majors,id',
        ]);

        if($validator->fails()){

            return $this->fail($validator->errors()->first());

        }

        $patient = Patient::create([
            'name'=>$request->name,
            'phone'=>$request->phone,
            'address'=>$request->address,
            'age'=>$request->age,
            'email'=>$request->email,
            'blood_type'=>$request->blood_type,
            'gender'=>$request->gender,
            'disease_type'=>$request->disease_type,
            'medical_history'=>$request->medical_history,
            'medical_recommendations'=>$request->medical_recommendations,
            'user_id'=>$request->user_id,
            'nclinic_id'=>$request->nclinic_id,
            'doctor_id'=>$request->doctor_id,
            
        ]);
        return $this->success( new PatientResource($patient) );
    }

    public function all()
    {
        $patient = Patient::all();
        return $this->success( PtientResource::collection($patient) );
    }
}
*/