<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    private function validateReservation(Request $request)
    {
        return $request->validate([
            'date' => 'required|date|after:today',
            'time' => ['required', 'date_format:H:i', function($attribute, $value, $fail) use ($request) {
                if ($request->filled('doctor_id') && $request->filled('date')) {
                    $existingReservation = Reservation::where('doctor_id', $request->doctor_id)
                        ->where('date', $request->date)
                        ->where('time', $value)
                        ->where('id', '!=', $request->reservation->id ?? null)
                        ->exists();
                    
                    if ($existingReservation) {
                        $fail('Doctor is not available at this time.');
                    }
                }
            }],
            'duration_minutes' => 'required|integer|min:15|max:180',
            'status' => 'required|in:pending,confirmed,cancelled',
            'reason_for_visit' => 'required|string',
            'notes' => 'nullable|string',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'nclinic_id' => 'required|exists:nclinics,id'
        ]);
    }

    public function index()
    {
        try {
            $reservations = Reservation::with(['patient.user', 'doctor.user', 'nclinic'])
                ->orderBy('date')
                ->orderBy('time')
                ->paginate(10);
            return response()->json(['status' => true, 'data' => $reservations]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateReservation($request);
            $reservation = Reservation::create($validated);
            return response()->json(['status' => true, 'data' => $reservation], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Reservation $reservation)
    {
        return response()->json([
            'status' => true,
            'data' => $reservation->load(['patient.user', 'doctor.user', 'nclinic', 'disease'])
        ]);
    }

    public function update(Request $request, Reservation $reservation)
    {
        try {
            $validated = $this->validateReservation($request);
            $reservation->update($validated);
            return response()->json(['status' => true, 'data' => $reservation]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Reservation $reservation)
    {
        try {
            $reservation->delete();
            return response()->json(['status' => true, 'message' => 'Reservation deleted']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
