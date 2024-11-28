<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{
   private function validateDisease(Request $request)
   {
       return $request->validate([
           'name' => 'required|string|max:255',
           'classification' => 'required|string',
           'type' => 'required|string',
           'description' => 'required|string', 
           'symptoms' => 'required|string',
           'treatment_protocol' => 'required|string',
           'doctor_id' => 'required|exists:doctors,id',
           'patients_id' => 'required|exists:patients,id'
       ]);
   }

   public function index(Request $request)
   {
       try {
           $query = Disease::with(['doctor.user', 'patient.user']);

           if ($request->has('doctor_id')) {
               $query->where('doctor_id', $request->doctor_id);
           }

           if ($request->has('patient_id')) {
               $query->where('patients_id', $request->patient_id);
           }

           if ($request->has('classification')) {
               $query->where('classification', $request->classification);
           }

           if ($request->has('type')) {
               $query->where('type', $request->type);
           }

           if ($request->has('name')) {
               $query->where('name', 'like', '%' . $request->name . '%');
           }

           $diseases = $query->latest()->paginate(10);

           return response()->json([
               'status' => true,
               'data' => $diseases
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function store(Request $request)
   {
       try {
           $validated = $this->validateDisease($request);
           
           $existingDisease = Disease::where('patients_id', $validated['patients_id'])
                                   ->where('name', $validated['name'])
                                   ->exists();

           if ($existingDisease) {
               return response()->json([
                   'status' => false,
                   'message' => 'المريض مشخص بهذا المرض مسبقاً'
               ], 422);
           }

           $disease = Disease::create($validated);

           return response()->json([
               'status' => true,
               'message' => 'تم إضافة التشخيص بنجاح',
               'data' => $disease
           ], 201);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function show(Disease $disease)
   {
       try {
           $disease->load(['doctor.user', 'patient.user']);
           
           return response()->json([
               'status' => true,
               'data' => $disease
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function update(Request $request, Disease $disease)
   {
       try {
           if ($disease->doctor_id !== auth()->user()->doctor->id) {
               return response()->json([
                   'status' => false,
                   'message' => 'غير مصرح لك بتعديل هذا التشخيص'
               ], 403);
           }

           $validated = $this->validateDisease($request);
           $disease->update($validated);

           return response()->json([
               'status' => true,
               'message' => 'تم تحديث التشخيص بنجاح',
               'data' => $disease
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function destroy(Disease $disease)
   {
       try {
           if ($disease->doctor_id !== auth()->user()->doctor->id) {
               return response()->json([
                   'status' => false,
                   'message' => 'غير مصرح لك بحذف هذا التشخيص'
               ], 403);
           }

           $disease->delete();

           return response()->json([
               'status' => true,
               'message' => 'تم حذف التشخيص بنجاح'
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function getPatientDiseases($patientId)
   {
       try {
           $diseases = Disease::where('patients_id', $patientId)
                            ->with(['doctor.user'])
                            ->latest()
                            ->get();

           return response()->json([
               'status' => true,
               'data' => $diseases
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function getDoctorDiagnoses()
   {
       try {
           $diseases = Disease::where('doctor_id', auth()->user()->doctor->id)
                            ->with(['patient.user'])
                            ->latest()
                            ->get();

           return response()->json([
               'status' => true,
               'data' => $diseases
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }
}