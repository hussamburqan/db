<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Resources\AppointmentResource;

class AppointmentController extends Controller
{
    private function validateAppointment(Request $request)
    {
        return $request->validate([
            'date' => 'required|date|after:today',
            'time' => ['required', 'date_format:H:i', function($attribute, $value, $fail) use ($request) {
                // Check if doctor is available at this time
                if ($request->filled('doctor_id') && $request->filled('date')) {
                    $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
                        ->where('date', $request->date)
                        ->where('time', $value)
                        ->where('id', '!=', $request->appointment->id ?? null)
                        ->exists();
                    
                    if ($existingAppointment) {
                        $fail('Doctor is not available at this time.');
                    }
                }
            }],
            'description' => 'required|string',
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
            'instructions' => 'required|string',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'nclinic_id' => 'required|exists:nclinics,id'
        ]);
    }

    public function index()
    {
        try {
            $appointments = Appointment::with(['doctor', 'patient.user', 'nclinic'])
                ->paginate(10);
                
            return response()->json([
                'status' => true,
                'data' => AppointmentResource::collection($appointments)
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
        // التحقق من صحة البيانات
        $validated = $this->validateAppointment($request);

        // جلب وقت فتح وإغلاق العيادة
        $nclinic = \App\Models\NClinic::find($request->nclinic_id);

        if (!$nclinic) {
            return response()->json([
                'status' => false,
                'message' => 'Clinic not found',
            ], 404);
        }

        // تحويل وقت الفتح والإغلاق إلى كائنات وقت
        $openingTime = \Carbon\Carbon::createFromFormat('H:i:s', $nclinic->opening_time);
        $closingTime = \Carbon\Carbon::createFromFormat('H:i:s', $nclinic->closing_time);
        $appointmentTime = \Carbon\Carbon::createFromFormat('H:i', $request->time);

        // التحقق من أن وقت الموعد ضمن ساعات العمل
        if ($appointmentTime->lt($openingTime) || $appointmentTime->gt($closingTime)) {
            return response()->json([
                'status' => false,
                'message' => 'Appointment time must be within clinic working hours (' . $nclinic->opening_time . ' - ' . $nclinic->closing_time . ')',
            ], 422);
        }

        // إنشاء الموعد
        $appointment = Appointment::create($validated);

        return response()->json([
            'status' => true,
            'data' => new AppointmentResource($appointment->load(['doctor', 'patient.user', 'nclinic']))
        ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => false,
            'message' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}


    public function show(Appointment $appointment)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new AppointmentResource($appointment->load(['doctor', 'patient.user', 'nclinic']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Appointment $appointment)
    {
        try {
            $validated = $this->validateAppointment($request);
            $appointment->update($validated);
            return response()->json(['status' => true, 'data' => $appointment]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Appointment $appointment)
    {
        try {
            if ($appointment->status === 'completed') {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete completed appointment'
                ], 422);
            }
            $appointment->delete();
            return response()->json(['status' => true, 'message' => 'Appointment deleted']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getDoctorSchedule(Request $request)
    {
        try {
            $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'date' => 'required|date'
            ]);

            $schedule = Appointment::where('doctor_id', $request->doctor_id)
                ->where('date', $request->date)
                ->orderBy('time')
                ->get(['time', 'status']);

            return response()->json(['status' => true, 'data' => $schedule]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
