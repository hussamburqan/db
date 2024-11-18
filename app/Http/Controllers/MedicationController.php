<?php

namespace App\Http\Controllers;

use App\Http\Resources\MedicationResource;
use App\Http\Traits\MobileResponse;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    use MobileResponse;

    public function update(Request $request,$id)
    {
        $medication = Medication::find($id);
        if(!$medication){
            return $this ->fail("not Found", 404);
        }


        $medication->update([
            
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,

        ]);
        return $this->success( new MedicationResource($medication) );
    }

    public function delete($id)
    {
        $medication = Medication::find($id);
        if($medication){
            $medication->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found");
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'description'=>'required',
            'price'=>'required',
            'quantity'=>'required',
            'patient_id'=>'required|exists:patients,id',
            'doctor_id'=>'required|exists:doctors,id',
        ]);

        if($validator->fails()){

            return $this->fail($validator->errors()->first());

        }

        $medication = Medication::create([
            'name'=>$request->name,
            'description'=>$request->description,
            'price'=>$request->price,
            'quantity'=>$request->quantity,
            'doctor_id'=>$request->doctor_id,
            'patient_id'=>$request->patient_id,
            
        ]);
        return $this->success( new MedicationResource($medication) );
    }

    public function all()
    {
        $medications = Medication::all();
        return $this->success( MedicationResource::collection($medication) );
    }
}

/*
    <?php

namespace App\Http\Controllers;

use App\Http\Resources\MedicationResource;
use App\Http\Traits\MobileResponse;
use App\Models\Medication;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    use MobileResponse;

    public function update(Request $request, $id)
    {
        $medication = Medication::find($id);
        if (!$medication) {
            return $this->fail("Not Found", 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'quantity' => 'sometimes|required|integer'
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first(), 400);
        }

        $medication->update($request->only(['name', 'description', 'price', 'quantity']));
        return $this->success(new MedicationResource($medication));
    }

    public function delete($id)
    {
        $medication = Medication::find($id);
        if ($medication) {
            $medication->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found", 404);
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first(), 400);
        }

        $medication = Medication::create($request->only(
            [
             'name',
             'description', 
             'price', 
             'quantity', 
             'patient_id', 
             'doctor_id'])
            );
        return $this->success(new MedicationResource($medication));
    }

    public function all()
    {
        $medications = Medication::all();
        return $this->success(MedicationResource::collection($medications));
    }
}

*/