<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'reservation_id' => 'required|exists:reservations,id'
        ]);
    }

    public function index()
    {
        try {
            $diseases = Disease::with(['doctor.user', 'reservation'])->paginate(10);
            return response()->json(['status' => true, 'data' => $diseases]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateDisease($request);
            $disease = Disease::create($validated);
            return response()->json(['status' => true, 'data' => $disease], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Disease $disease)
    {
        return response()->json([
            'status' => true,
            'data' => $disease->load(['doctor.user', 'reservation.patient'])
        ]);
    }

    public function update(Request $request, Disease $disease)
    {
        try {
            $validated = $this->validateDisease($request);
            $disease->update($validated);
            return response()->json(['status' => true, 'data' => $disease]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Disease $disease)
    {
        try {
            $disease->delete();
            return response()->json(['status' => true, 'message' => 'Disease deleted']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}