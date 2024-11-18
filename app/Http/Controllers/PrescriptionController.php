<?php

namespace App\Http\Controllers;


use App\Http\Resources\PrescriptionResource;
use App\Http\Traits\MobileResponse;
use App\Models\Prescription;
use App\Models\Medication;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    use MobileResponse;

    public function update(Request $request,$id)
    {
        $prescription = Prescription::find($id);
        if(!$prescription){
            return $this ->fail("not Found");
        }


        $prescription->update([
            
            'date' => $request->date,
            'medications' => $request->medications,
            'instructions' => $request->instructions,
            
        ]);
        return $this->success( new InvoiceResource($disease) );
    }

    public function delete($id)
    {
        $prescription = Prescription::find($id);
        if($prescription){
            $prescription->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found");
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'date'=>'required',
            'medications'=>'required',
            'instructions'=>'required',
            'doctor_id'=>'required|exists:doctor,id',
            'patient_id'=>'required|exists:patients,id',
            'medication_id'=>'required|exists:medications,id',
            'user_id'=>'required|exists:users,id',
        ]);

        if($validator->fails()){

            return $this->fail($validator->errors()->first());

        }

        $prescription = Prescription::create([
            'date'=>$request->date,
            'medications'=>$request->medications,
            'instructions'=>$request->instructions,
            'medication_id'=>$request->medication_id,
            'user_id'=>$request->user_id,
            'doctor_id'=>$request->doctor_id,
            'patient_id'=>$request->patient_id,
            
        ]);
        return $this->success( new PrescriptionResource($prescription) );
    }

    public function all()
    {
        $prescriptions = Prescription::all();
        return $this->success( PriscriptionResource::collection($prescription) );
    }
}
