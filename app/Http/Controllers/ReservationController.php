<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ReservationResource;
use Illuminate\Support\Facades\Storage;


class ReservationController extends Controller
{
    public function getAvailableSlots(Request $request)
    {
        try {
            \Log::info('Starting getAvailableSlots', $request->all());
    
            $validated = $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'date' => [
                    'required',
                    'date',
                    'after:today',
                    'before_or_equal:' . now()->addDays(30)->format('Y-m-d')
                ],
                'patient_id' => 'required|exists:patients,id'
            ]);
            $hasReservation = Reservation::where('patient_id', $validated['patient_id'])
            ->where('date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($hasReservation) {
            return response()->json([
                'status' => true,
                'data' => [] 
            ]);
        }
            $doctor = Doctor::findOrFail($validated['doctor_id']);
            
            \Log::info('Doctor working hours', [
                'start' => $doctor->start_work_time,
                'end' => $doctor->end_work_time
            ]);
    
            $bookedSlots = Reservation::where('doctor_id', $doctor->id)
                ->where('date', $validated['date'])
                ->where('status', '!=', 'cancelled')
                ->get();
    
            \Log::info('Booked slots', $bookedSlots->toArray());
    
            $availableSlots = [];
            $currentTime = Carbon::createFromFormat('H:i:s', $doctor->start_work_time);
            $endTime = Carbon::createFromFormat('H:i:s', $doctor->end_work_time);
            $interval = $doctor->default_time_reservations ?? 30; 
    
            while ($currentTime < $endTime) {
                $timeSlot = $currentTime->format('H:i');
                $isAvailable = true;
    
                foreach ($bookedSlots as $booking) {
                    $bookingStart = Carbon::createFromFormat('H:i:s', $booking->time);
                    $bookingEnd = $bookingStart->copy()->addMinutes($booking->duration_minutes);
    
                    if ($currentTime >= $bookingStart && $currentTime < $bookingEnd) {
                        $isAvailable = false;
                        break;
                    }
                }
    
                if ($isAvailable) {
                    $availableSlots[] = $timeSlot;
                }
    
                $currentTime->addMinutes($interval);
            }
    
            \Log::info('Available slots', $availableSlots);
    
            return response()->json([
                'status' => true,
                'data' => $availableSlots
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Error in getAvailableSlots: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = Reservation::with(['patient.user', 'doctor.user', 'clinic']);
    
            $reservations = $query->orderBy('date', 'asc')
                                  ->orderBy('time', 'asc')
                                  ->get();
    
            return response()->json([
                'status' => true,
                'data' => ReservationResource::collection($reservations),
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Error fetching reservations:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request)
{
    try {
        \Log::info('Search parameters:', $request->all()); 
        
        $query = Reservation::with(['patient.user', 'doctor.user', 'clinic']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        $reservations = $query->orderBy('date', 'asc')
                              ->orderBy('time', 'asc')
                              ->get();

        if ($reservations->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'No reservations found with the given criteria',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => ReservationResource::collection($reservations)
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error in search:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => false,
            'message' => 'An error occurred during search',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function updateStatus(Request $request, $id)
{
    try {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => $validated['status']]);

        return response()->json([
            'status' => true,
            'message' => 'Reservation status updated successfully'
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
            DB::beginTransaction();

            $validated = $this->validateReservation($request);

            $existingReservation = Reservation::where('patient_id', $validated['patient_id'])
                ->where('date', $validated['date'])
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($existingReservation) {
                return response()->json([
                    'status' => false,
                    'message' => 'You already have a reservation on this date'
                ], 422);
            }

            $doctor = Doctor::find($validated['doctor_id']);
            if (!isset($validated['duration_minutes'])) {
                $validated['duration_minutes'] = $doctor->default_time_reservations;
            }

            $reservation = Reservation::create($validated);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Reservation created successfully',
                'data' => new ReservationResource($reservation)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Reservation $reservation)
    {
        try {
            DB::beginTransaction();
    
            $reservation->delete();
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Reservation deleted successfully'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show(Reservation $reservation)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new ReservationResource($reservation->load(['patient.user', 'doctor.user', 'clinic']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePatientArchive(Request $request, $archiveId)
{
    try {
        // Validate the incoming request
        $validatedData = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'description' => 'required|string',
            'instructions' => 'required|string',
            'status' => 'required|in:completed,pending,cancelled'
        ]);

        // Find the existing archive
        $patientArchive = PatientArchive::findOrFail($archiveId);

        // Optional: Verify reservation belongs to the patient
        $reservation = Reservation::findOrFail($validatedData['reservation_id']);
        if ($reservation->patient_id !== $validatedData['patient_id']) {
            return response()->json([
                'status' => false,
                'message' => 'Reservation does not belong to the specified patient'
            ], 400);
        }

        // Update the patient archive
        $patientArchive->update([
            'reservation_id' => $validatedData['reservation_id'],
            'patient_id' => $validatedData['patient_id'],
            'doctor_id' => $validatedData['doctor_id'],
            'description' => $validatedData['description'],
            'instructions' => $validatedData['instructions'],
            'status' => $validatedData['status'],
            'date' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Patient archive updated successfully',
            'data' => $patientArchive
        ]);

    } catch (ValidationException $e) {
        // Handle validation errors
        return response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);

    } catch (ModelNotFoundException $e) {
        // Handle not found errors
        return response()->json([
            'status' => false,
            'message' => 'Patient archive or related record not found'
        ], 404);

    } catch (\Exception $e) {
        // Catch any other unexpected errors
        return response()->json([
            'status' => false,
            'message' => 'Error updating patient archive: ' . $e->getMessage()
        ], 500);
    }
}


    public function accepted(Reservation $reservation)
    {
        try {
            $reservation->update(['status' => 'accepted']);

            return back()->with('success', 'تم تأكيد الموعد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تأكيد الموعد');
        }
    }

    public function reject(Reservation $reservation)
    {
        try {
            $reservation->update(['status' => 'cancelled']);

            return back()->with('success', 'تم رفض الموعد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء رفض الموعد');
        }
    }
    public function cancel(Reservation $reservation)
    {
        try {
            if ($reservation->status === 'cancelled') {
                return response()->json([
                    'status' => false,
                    'message' => 'Reservation is already cancelled'
                ], 422);
            }

            DB::beginTransaction();

            $reservation->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Reservation cancelled successfully',
                'data' => new ReservationResource($reservation)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function validateReservation(Request $request)
    {
        return $request->validate([
            'date' => [
                'required',
                'date',         
                //'after:today',
                'before_or_equal:' . now()->addDays(30)->format('Y-m-d')
            ],
            'time' => [
                'required',
                'date_format:H:i',
                function($attribute, $value, $fail) use ($request) {
                    if ($request->filled('doctor_id') && $request->filled('date')) {
                        $doctor = Doctor::find($request->doctor_id);
                        
                        $requestTime = Carbon::createFromFormat('H:i', $value);
                        $startTime = Carbon::createFromFormat('H:i:s', $doctor->start_work_time)->format('H:i');
                        $endTime = Carbon::createFromFormat('H:i:s', $doctor->end_work_time)->format('H:i');
    
                        if ($value < $startTime || $value >= $endTime) {
                            $fail("Time must be between {$startTime} and {$endTime}");
                            return;
                        }
    
                        $appointmentStart = $requestTime;
                        $appointmentEnd = $requestTime->copy()
                            ->addMinutes($request->duration_minutes ?? $doctor->default_time_reservations);
    
                        $existingAppointment = Reservation::where('doctor_id', $request->doctor_id)
                            ->where('date', $request->date)
                            ->where('status', '!=', 'cancelled')
                            ->where(function($query) use ($appointmentStart, $appointmentEnd) {
                                $query->where(function($q) use ($appointmentStart, $appointmentEnd) {
                                    $q->whereTime('time', '<', $appointmentEnd->format('H:i'))
                                      ->whereRaw("ADDTIME(time, SEC_TO_TIME(duration_minutes * 60)) > ?", 
                                          [$appointmentStart->format('H:i')]);
                                });
                            })
                            ->exists();
    
                        if ($existingAppointment) {
                            $fail('This time slot is already booked');
                        }
                    }
                }
            ],
            'duration_minutes' => 'required|integer|min:15|max:120',
            'status' => 'required|in:pending,accepted,confirmed,cancelled',
            'reason_for_visit' => 'required|string',
            'notes' => 'nullable|string',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'nclinic_id' => 'required|exists:nclinics,id'
        ]);
    }
}
