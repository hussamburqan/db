<?php

namespace App\Http\Controllers;

use App\Models\PatientArchive;
use App\Models\Doctor;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Resources\PatientArchiveResource;

class PatientArchiveController extends Controller
{
    private function validateArchive(Request $request)
    {
        return $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'status' => 'required|string',
            'instructions' => 'required|string',
            'reservation_id' => 'required|exists:reservations,id',
            'doctor_id' => 'required|exists:doctors,id'
        ]);
    }
    public function index(Request $request)
{
    try {
        $query = PatientArchive::with(['doctor', 'reservation', 'reservation.patient.user']);

        // إضافة شروط البحث
        if ($request->has('reservation_id')) {
            $query->where('reservation_id', $request->reservation_id);
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

        if ($request->has('patient_name')) {
            $query->whereHas('reservation.patient.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->patient_name . '%');
            });
        }

        $archives = $query->orderBy('date', 'desc')->paginate(10);

        return response()->json([
            'status' => true,
            'data' => PatientArchiveResource::collection($archives)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء استرجاع الأرشيف: ' . $e->getMessage()
        ], 500);
    }
}

    
    public function store(Request $request)
    {
        try {
            $validated = $this->validateArchive($request);

            $doctor = Doctor::findOrFail($validated['doctor_id']);

            $archive = PatientArchive::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'تم إنشاء السجل بنجاح',
                'data' => new PatientArchiveResource($archive)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
{
    try {
        $archive = PatientArchive::with(['doctor.user', 'patient.user'])->find($id);

        if (!$archive) {
            return response()->json([
                'status' => false,
                'message' => 'Archive not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => new PatientArchiveResource($archive)
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
        if (!$archive) {
            return response()->json([
                'status' => false,
                'message' => 'السجل غير موجود'
            ], 404);
        }

        if ($archive->status === 'completed') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن تحديث سجل مكتمل'
            ], 422);
        }

        $validated = $this->validateArchive($request);

        $archive->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث السجل بنجاح',
            'data' => new PatientArchiveResource($archive)
        ]);
    } catch (\Exception $e) {
        // تسجيل الأخطاء
        \Log::error('Error updating archive: ' . $e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()
        ], 500);
    }
}


public function destroy(PatientArchive $archive)
{
    try {
        if (!$archive) {
            return response()->json([
                'status' => false,
                'message' => 'السجل غير موجود'
            ], 404);
        }

        if ($archive->status === 'completed') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن حذف سجل مكتمل'
            ], 422);
        }

        $archive->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف السجل بنجاح'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error deleting archive: ' . $e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()
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
                ->get(['date', 'status']);

            return response()->json([
                'status' => true,
                'data' => [
                    'work_hours' => [
                        'start' => $doctor->start_work_time,
                        'end' => $doctor->end_work_time,
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
