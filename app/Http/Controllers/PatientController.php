<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Resources\PatientResource;

class PatientController extends Controller
{
    private function validatePatient(Request $request)
    {
        return $request->validate([
            'emergency_contact' => 'required|string',
            'emergency_phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'medical_history' => 'required|string',
            'allergies' => 'required|string',
            'current_medications' => 'required|string',
            'medical_recommendations' => 'required|string',
            'user_id' => 'required|exists:users,id|unique:patients,user_id,' . ($request->patient->id ?? '')
        ]);
    }

    public function index()
    {
        try {
            $patients = Patient::with(['user', 'appointments'])->paginate(10);
            return response()->json(['status' => true, 'data' => $patients]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validatePatient($request);
            $patient = Patient::create($validated);
            return response()->json(['status' => true, 'data' => $patient->load('user')], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Patient $patient)
    {
        try {
            $patient->load(['user', 'appointments.doctor', 'appointments.nclinic']);
            return response()->json([
                'status' => true,
                'data' => new PatientResource($patient)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Patient $patient)
    {
        try {
            $validated = $this->validatePatient($request);
            $patient->update($validated);
            return response()->json(['status' => true, 'data' => $patient]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Patient $patient)
    {
        try {
            if ($patient->appointments()->where('status', 'scheduled')->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete patient with active appointments'
                ], 422);
            }
            $patient->delete();
            return response()->json(['status' => true, 'message' => 'Patient deleted']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}