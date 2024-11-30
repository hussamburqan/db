<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Http\Resources\DoctorResource;
use App\Models\NClinic;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DoctorController extends Controller
{
    private function validateDoctor(Request $request, $isUpdate = false)
    {
        return $request->validate([
            'experience_years' => 'required|integer|min:0',
            'specialization' => 'required|string',
            'education' => 'required|string',
            'photo' => $isUpdate ? 'nullable|image|mimes:jpeg,png,jpg|max:2048' : 'required|image|mimes:jpeg,png,jpg|max:2048',
            'start_work_time' => ['required', 'date_format:H:i', function($attribute, $value, $fail) use ($request) {
                if ($request->filled('end_work_time') && $value >= $request->end_work_time) {
                    $fail('وقت بدء العمل يجب أن يكون قبل وقت الانتهاء');
                }
            }],
            'end_work_time' => ['required', 'date_format:H:i', function($attribute, $value, $fail) use ($request) {
                if ($request->filled('start_work_time') && $value <= $request->start_work_time) {
                    $fail('وقت نهاية العمل يجب أن يكون بعد وقت البدء');
                }
            }],
            'default_time_reservations' => 'required|integer|min:15|max:120',
            'bio' => 'required|string',
            'major_id' => 'required|exists:majors,id',
            'nclinic_id' => 'required|exists:nclinics,id'
        ]);
    }

    public function index(Request $request)
    {
        try {
            $query = Doctor::with(['user', 'major', 'clinic']);
    
            if ($request->has('major_id')) {
                $query->where('major_id', $request->major_id);
            }
    
            if ($request->has('clinic_id')) {
                $query->where('nclinic_id', $request->clinic_id);
            }
    
            if ($request->has('name')) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->name . '%');
                });
            }
    
            if ($request->has('specialization')) {
                $query->where('specialization', 'like', '%' . $request->specialization . '%');
            }
    
            $doctors = $query->paginate(10);
    
            return response()->json([
                'status' => true,
                'data' => $doctors  
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
    
            $userValidated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|confirmed',
                'address' => 'required|string',
                'age' => 'required|integer|between:1,120',
                'gender' => 'required|in:male,female',
                'phone' => 'required|string'
            ]);
    
            $userValidated['password'] = bcrypt($userValidated['password']);
            $user = User::create($userValidated);
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            $doctorValidated = $this->validateDoctor($request);
    
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('doctors', 'public');
                $doctorValidated['photo'] = $photoPath;
            }
    
            $clinic = NClinic::find($doctorValidated['nclinic_id']);
            if ($doctorValidated['start_work_time'] < $clinic->opening_time || 
                $doctorValidated['end_work_time'] > $clinic->closing_time) {
                return response()->json([
                    'status' => false,
                    'message' => 'أوقات عمل الطبيب يجب أن تكون ضمن أوقات عمل العيادة'
                ], 422);
            }
    
            $doctorValidated['user_id'] = $user->id;
            $doctor = Doctor::create($doctorValidated);
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'تم إضافة الطبيب بنجاح',
                'data' => new DoctorResource($doctor->load(['user', 'major', 'clinic'])),
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    

    public function show(Doctor $doctor)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new DoctorResource($doctor->load(['user', 'major', 'clinic']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Doctor $doctor)
    {
        try {
            $validated = $this->validateDoctor($request, true);

            if ($request->hasFile('photo')) {
                if ($doctor->photo) {
                    Storage::disk('public')->delete($doctor->photo);
                }
                $photoPath = $request->file('photo')->store('doctors', 'public');
                $validated['photo'] = $photoPath;
            }

            $clinic = NClinic::find($validated['n_clinic_id']);
            if ($validated['start_work_time'] < $clinic->opening_time || 
                $validated['end_work_time'] > $clinic->closing_time) {
                return response()->json([
                    'status' => false,
                    'message' => 'أوقات عمل الطبيب يجب أن تكون ضمن أوقات عمل العيادة'
                ], 422);
            }

            $doctor->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث بيانات الطبيب بنجاح',
                'data' => new DoctorResource($doctor)
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
            if ($doctor->archives()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يمكن حذف طبيب لديه سجل مواعيد'
                ], 422);
            }

            if ($doctor->photo) {
                Storage::disk('public')->delete($doctor->photo);
            }

            $doctor->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الطبيب بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableSlots(Request $request)
{
    try {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today'
        ]);

        $doctor = Doctor::findOrFail($request->doctor_id);
        
        // Get existing reservations for the date
        $bookedTimes = Reservation::where('doctor_id', $doctor->id)
            ->where('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->pluck('time')
            ->toArray();

        // Also check archived appointments
        $archivedTimes = PatientArchive::where('doctor_id', $doctor->id)
            ->where('date', $request->date)
            ->pluck('time')
            ->toArray();

        $bookedTimes = array_merge($bookedTimes, $archivedTimes);

        // Generate all possible time slots
        $slots = [];
        $current = Carbon::createFromFormat('H:i', $doctor->start_work_time);
        $end = Carbon::createFromFormat('H:i', $doctor->end_work_time);

        // Subtract duration from end time to ensure last appointment fits
        $end = $end->subMinutes($doctor->default_time_reservations);

        while ($current <= $end) {
            $timeSlot = $current->format('H:i');
            
            // Check if this slot is available
            $slotEnd = $current->copy()->addMinutes($doctor->default_time_reservations);
            $isAvailable = true;

            foreach ($bookedTimes as $bookedTime) {
                $bookingStart = Carbon::createFromFormat('H:i', $bookedTime);
                $bookingEnd = $bookingStart->copy()->addMinutes($doctor->default_time_reservations);

                // Check if slots overlap
                if ($current < $bookingEnd && $slotEnd > $bookingStart) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $slots[] = $timeSlot;
            }

            $current->addMinutes($doctor->default_time_reservations);
        }

        return response()->json([
            'status' => true,
            'data' => $slots
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}