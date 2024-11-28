<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    private function validateReservation(Request $request)
    {
        return $request->validate([
            'date' => 'required|date|after:today',
            'time' => ['required', 'date_format:H:i', function($attribute, $value, $fail) use ($request) {
                if ($request->filled('doctor_id') && $request->filled('date')) {
                    // التحقق من أن الوقت متاح
                    $existingReservation = Reservation::where('doctor_id', $request->doctor_id)
                        ->where('date', $request->date)
                        ->where('time', $value)
                        ->exists();

                    $existingArchive = PatientArchive::where('doctor_id', $request->doctor_id)
                        ->where('date', $request->date)
                        ->where('time', $value)
                        ->exists();
                    
                    if ($existingReservation || $existingArchive) {
                        $fail('هذا الوقت محجوز مسبقاً');
                    }

                    // التحقق من أوقات عمل الطبيب
                    $doctor = Doctor::find($request->doctor_id);
                    $requestTime = Carbon::createFromFormat('H:i', $value);
                    
                    if ($requestTime->format('H:i') < $doctor->start_work_time || 
                        $requestTime->format('H:i') > $doctor->end_work_time) {
                        $fail('الوقت خارج ساعات عمل الطبيب');
                    }
                }
            }],
            'duration_minutes' => 'required|integer|min:15|max:120',
            'status' => 'required|in:pending,confirmed,cancelled',
            'reason_for_visit' => 'required|string',
            'notes' => 'nullable|string',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'nclinic_id' => 'required|exists:nclinics,id'
        ]);
    }

    public function index(Request $request)
    {
        try {
            $query = Reservation::with(['patient.user', 'doctor.user', 'clinic']);

            // Filter by patient
            if ($request->has('patient_id')) {
                $query->where('patient_id', $request->patient_id);
            }

            // Filter by doctor
            if ($request->has('doctor_id')) {
                $query->where('doctor_id', $request->doctor_id);
            }

            // Filter by clinic
            if ($request->has('clinic_id')) {
                $query->where('nclinic_id', $request->clinic_id);
            }

            // Filter by date
            if ($request->has('date')) {
                $query->whereDate('date', $request->date);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $reservations = $query->orderBy('date', 'asc')
                                ->orderBy('time', 'asc')
                                ->paginate(10);

            return response()->json([
                'status' => true,
                'data' => ReservationResource::collection($reservations)
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
            $validated = $this->validateReservation($request);

            // التحقق من أن المريض ليس لديه حجز آخر في نفس اليوم
            $existingReservation = Reservation::where('patient_id', $validated['patient_id'])
                ->where('date', $validated['date'])
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($existingReservation) {
                return response()->json([
                    'status' => false,
                    'message' => 'لديك حجز آخر في نفس اليوم'
                ], 422);
            }

            $doctor = Doctor::find($validated['doctor_id']);
            
            // استخدام مدة الحجز الافتراضية للطبيب إذا لم يتم تحديد المدة
            if (!isset($validated['duration_minutes'])) {
                $validated['duration_minutes'] = $doctor->default_time_reservations;
            }

            $reservation = Reservation::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'تم إنشاء الحجز بنجاح',
                'data' => new ReservationResource($reservation)
            ], 201);
        } catch (\Exception $e) {
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

    public function update(Request $request, Reservation $reservation)
    {
        try {
            // التحقق من أن الحجز لم يتم إلغاؤه
            if ($reservation->status === 'cancelled') {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يمكن تعديل حجز تم إلغاؤه'
                ], 422);
            }

            $validated = $this->validateReservation($request);
            $reservation->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث الحجز بنجاح',
                'data' => new ReservationResource($reservation)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Reservation $reservation)
    {
        try {
            if ($reservation->status === 'confirmed') {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يمكن حذف حجز مؤكد'
                ], 422);
            }

            $reservation->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الحجز بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Reservation $reservation)
    {
        try {
            if ($reservation->status === 'cancelled') {
                return response()->json([
                    'status' => false,
                    'message' => 'الحجز ملغي بالفعل'
                ], 422);
            }

            $reservation->update(['status' => 'cancelled']);

            return response()->json([
                'status' => true,
                'message' => 'تم إلغاء الحجز بنجاح',
                'data' => new ReservationResource($reservation)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function confirm(Reservation $reservation)
    {
        try {
            if ($reservation->status !== 'pending') {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يمكن تأكيد هذا الحجز'
                ], 422);
            }

            $reservation->update(['status' => 'confirmed']);

            return response()->json([
                'status' => true,
                'message' => 'تم تأكيد الحجز بنجاح',
                'data' => new ReservationResource($reservation)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}