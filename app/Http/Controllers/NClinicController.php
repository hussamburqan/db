<?php
// app/Http/Controllers/NClinicController.php

namespace App\Http\Controllers;

use App\Http\Resources\NClinicResource;
use App\Http\Traits\MobileResponse;
use App\Models\NClinic;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class NClinicController extends Controller
{
    use MobileResponse;

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'location' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
            'email' => 'required|email|unique:nclinics',
            'phone' => 'required|integer|unique:nclinics',
            'major_id' => 'required|exists:majors,id',
            'user_id' => 'required|exists:users,id',
            'patient_id' => 'required|exists:patients,id'
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        $nclinic = NClinic::create([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'email' => $request->email,
            'phone' => $request->phone,
            'major_id' => $request->major_id,
            'user_id' => $request->user_id,
            'patient_id' => $request->patient_id
        ]);

        return $this->success(new NClinicResource($nclinic));
    }

    public function update(Request $request, $id)
    {
        $nclinic = NClinic::find($id);
        if (!$nclinic) {
            return $this->fail("Clinic not found");
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'location' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'start_date' => 'sometimes|required',
            'end_date' => 'sometimes|required',
            'status' => 'sometimes|required',
            'email' => 'sometimes|required|email|unique:nclinics,email,'.$id,
            'phone' => 'sometimes|required|integer|unique:nclinics,phone,'.$id,
            'major_id' => 'sometimes|required|exists:majors,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'patient_id' => 'sometimes|required|exists:patients,id'
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        $nclinic->update($request->only([
            'name', 'location', 'description', 'start_date', 
            'end_date', 'status', 'email', 'phone',
            'major_id', 'user_id', 'patient_id'
        ]));

        return $this->success(new NClinicResource($nclinic));
    }

    public function delete($id)
    {
        $nclinic = NClinic::find($id);
        if (!$nclinic) {
            return $this->fail("Clinic not found");
        }
        
        $nclinic->delete();
        return $this->success("Deleted successfully");
    }

    public function all()
    {
        $nclinics = NClinic::with(['major', 'user', 'patient'])->get();
        return $this->success(NClinicResource::collection($nclinics));
    }

    public function show($id)
    {
        $nclinic = NClinic::with(['major', 'user', 'patient'])->find($id);
        if (!$nclinic) {
            return $this->fail("Clinic not found");
        }
        return $this->success(new NClinicResource($nclinic));
    }
}