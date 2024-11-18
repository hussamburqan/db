<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiseaseResource;
use App\Http\Traits\MobileResponse;
use App\Models\Disease;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Major;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{

    use MobileResponse;

    public function update(Request $request, $id)
    {
        $disease = Disease::find($id);
        if(!$disease){
            return $this->fail("Not Found");
        }

        $disease->update([
            'disease_classification' => $request->disease_classification,
            'disease_type' => $request->disease_type,
            'description' => $request->description,
        ]);

        return $this->success(new DiseaseResource($disease));
    }

    public function delete($id)
    {
        $disease = Disease::find($id);
        if($disease){
            $disease->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found");
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'disease_classification' => 'required',
            'disease_type' => 'required',
            'description' => 'required',
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'major_id' => 'required|exists:majors,id',
        ]);

        if($validator->fails()){
            return $this->fail($validator->errors()->first());
        }

        $disease = Disease::create([
            'disease_classification' => $request->disease_classification,
            'disease_type' => $request->disease_type,
            'description' => $request->description,
            'doctor_id' => $request->doctor_id,
            'patient_id' => $request->patient_id,
            'major_id' => $request->major_id,
        ]);

        return $this->success(new DiseaseResource($disease));
    }

    public function all()
    {
        $diseases = Disease::all();
        return $this->success(DiseaseResource::collection($diseases));
    }
}

/*

*/

///////////////////////////////
///////////////////////////////
///////////////////////////////
///////////////////////////////
///////////////////////////////

/*
public function update(Request $request,$id)
    {
        $disease = Disease::find($id);
        if(!$disease){
            return $this ->fail("not Found");
        }


        $disease->update([
            'disease_classification' => $request->disease_classification,
            'disease_type' => $request->disease_type,
            'description' => $request->description,
        ]);
        return $this->success( new DiseaseResource($disease) );
    }

    public function delete($id)
    {
        $disease = Disease::find($id);
        if($disease){
            $disease->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found");
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'disease_classification'=>'required',
            'disease_type'=>'required',
            'description'=>'required',
            'doctor_id'=>'required|exists:doctors,id',
            'patient_id'=>'required|exists:patients,id',
            'major_id'=>'required|exists:majors,id',
        ]);

        if($validator->fails()){

            return $this->fail($validator->errors()->first());

        }

        $disease = Disease::create([
            'disease_classification'=>$request->disease_classification,
            'disease_type'=>$request->disease_type,
            'description'=>$request->description,
            'major_id'=>$request->major_id,
            'doctor_id'=>$request->doctor_id,
            'patient_id'=>$request->patient_id,
            
        ]);
        return $this->success( new DiseaseResource($disease) );
    }

    public function all()
    {
        $disease = Disease::all();
        return $this->success( DiseaseResource::collection($appointment) );
    }
*/