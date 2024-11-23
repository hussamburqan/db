<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    private function validateDoctor(Request $request, $isUpdate = false)
    {
        $rules = [
            'name' => 'required|string',
            'experience_years' => 'required|integer|min:0',
            'specialization' => 'required|string',
            'education' => 'required|string',
            'bio' => 'required|string',
            'major_id' => 'required|exists:majors,id',
            'n_clinic_id' => 'required|exists:nclinics,id',
            'photo' => $isUpdate ? 'nullable|image|mimes:jpeg,png,jpg|max:2048' : 'required|image|mimes:jpeg,png,jpg|max:2048'
        ];
        
        return $request->validate($rules);
    }

    public function index()
    {
        try {
            $doctors = Doctor::with(['user', 'major', 'nclinic'])->paginate(10);
            return response()->json(['status' => true, 'data' => $doctors]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateDoctor($request);
            
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('doctors', 'public');
                $validated['photo'] = $photoPath;
            }

            $doctor = Doctor::create($validated);
            
            return response()->json([
                'status' => true,
                'message' => 'Doctor created successfully',
                'data' => $doctor
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function show(Doctor $doctor)
    {
        return response()->json([
            'status' => true, 
            'data' => $doctor->load(['user', 'major', 'nclinic', 'appointments'])
        ]);
    }

    public function update(Request $request, Doctor $doctor)
    {
        try {
            $validated = $this->validateDoctor($request, true);
            
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($doctor->photo) {
                    Storage::disk('public')->delete($doctor->photo);
                }
                
                $photoPath = $request->file('photo')->store('doctors', 'public');
                $validated['photo'] = $photoPath;
            }

            $doctor->update($validated);
            
            return response()->json([
                'status' => true,
                'message' => 'Doctor updated successfully',
                'data' => $doctor
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

   
    public function destroy(Doctor $doctor)
    {
        try {
            if ($doctor->photo) {
                Storage::disk('public')->delete($doctor->photo);
            }
            
            $doctor->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Doctor deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
}