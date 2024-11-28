<?php

namespace App\Http\Controllers;

use App\Models\PatientArchive;
use Illuminate\Http\Request;
use App\Http\Resources\PatientArchiveResource;

class PatientArchiveController extends Controller
{
    private function validateArchive(Request $request)
    {
        return $request->validate([
            'date' => 'required|date|after:today',
            'time' => ['required', 'date_format:H:i', function($attribute, $value, $fail) use ($request) {
                if ($request->filled('doctor_id') && $request->filled('date')) {
                    $existingArchive = PatientArchive::where('doctor_id', $request->doctor_id)
                        ->where('date', $request->date)
                        ->where('time', $value)
                        ->where('id', '!=', $request->archive_id ?? null)
                        ->exists();
                    
                    if ($existingArchive) {
                        $fail('الطبيب لديه موعد في هذا الوقت');
                    }
                }
            }],
            'description' => 'required|string',
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
            'instructions' => 'required|string',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id'
        ]);
    }

    public function index(Request $request)
    {
        try {
            $query = PatientArchive::with(['doctor.user', 'patient.user']);

            if ($request->has('patient_id')) {
                $query->where('patient_id', $request->patient_id);
            }

            if ($request->has('doctor_id')) {
                $query->where('doctor_id', $request->doctor_id);
            }

            if ($request->has('date')) {
                $query->whereDate('date', $request->date);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $archives = $query->orderBy('date', 'desc')
                            ->orderBy('time', 'desc')
                            ->paginate(10);

            return response()->json([
                'status' => true,
                'data' => PatientArchiveResource::collection($archives)
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
            $validated = $this->validateArchive($request);
            
            $doctor = Doctor::find($validated['doctor_id']);
            $requestTime = \Carbon\Carbon::createFromFormat('H:i', $validated['time']);
            
            if ($requestTime->format('H:i') < $doctor->start_work_time || 
                $requestTime->format('H:i') > $doctor->end_work_time) {
                return response()->json([
                    'status' => false,
                    'message' => 'الوقت المطلوب خارج ساعات عمل الطبيب'
                ], 422);
            }

            $archive = PatientArchive::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'تم إنشاء الموعد بنجاح',
                'data' => new PatientArchiveResource($archive)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(PatientArchive $archive)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new PatientArchiveResource($archive->load(['doctor.user', 'patient.user']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, PatientArchive $archive)
    {
        try {
            $validated = $this->validateArchive($request);
            $archive->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث الموعد بنجاح',
                'data' => new PatientArchiveResource($archive)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(PatientArchive $archive)
    {
        try {
            if ($archive->status === 'completed') {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يمكن حذف موعد مكتمل'
                ], 422);
            }

            if ($archive->invoice()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يمكن حذف موعد له فاتورة'
                ], 422);
            }

            $archive->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الموعد بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDoctorSchedule(Request $request)
    {
        try {
            $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'date' => 'required|date'
            ]);

            $doctor = Doctor::findOrFail($request->doctor_id);
            
            $schedule = PatientArchive::where('doctor_id', $request->doctor_id)
                ->where('date', $request->date)
                ->orderBy('time')
                ->get(['time', 'status']);

            return response()->json([
                'status' => true,
                'data' => [
                    'work_hours' => [
                        'start' => $doctor->start_work_time,
                        'end' => $doctor->end_work_time,
                        'default_duration' => $doctor->default_time_reservations
                    ],
                    'appointments' => $schedule
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}