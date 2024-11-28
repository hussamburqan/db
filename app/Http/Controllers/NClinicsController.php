<?php

namespace App\Http\Controllers;

use App\Models\NClinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ClinicResource;

class NClinicsController extends Controller
{
    private function validateClinic(Request $request, $isUpdate = false)
    {
        return $request->validate([
            'location' => 'required|string',
            'photo' => $isUpdate ? 'nullable|image|mimes:jpeg,png,jpg|max:2048' : 'required|image|mimes:jpeg,png,jpg|max:2048',
            'cover_photo' => $isUpdate ? 'nullable|image|mimes:jpeg,png,jpg|max:2048' : 'required|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'required|string',
            'opening_time' => ['required', 'date_format:H:i', function($attribute, $value, $fail) use ($request) {
                if ($request->filled('closing_time') && $value >= $request->closing_time) {
                    $fail('وقت الفتح يجب أن يكون قبل وقت الإغلاق');
                }
            }],
            'closing_time' => ['required', 'date_format:H:i', function($attribute, $value, $fail) use ($request) {
                if ($request->filled('opening_time') && $value <= $request->opening_time) {
                    $fail('وقت الإغلاق يجب أن يكون بعد وقت الفتح');
                }
            }],
            'user_id' => $isUpdate ? 'exists:users,id' : 'required|exists:users,id|unique:nclinics',
            'major_id' => 'required|exists:majors,id'
        ]);
    }

    public function index(Request $request)
{
    try {
        $query = NClinic::with(['user', 'major', 'doctors']);

        // البحث والفلترة
        if ($request->has('major_id')) {
            $query->where('major_id', $request->major_id);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('time')) {
            $query->where('opening_time', '<=', $request->time)
                  ->where('closing_time', '>=', $request->time);
        }

        // البحث حسب اسم العيادة (من خلال اسم المستخدم المرتبط)
        if ($request->has('name')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        // الترتيب
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSortFields = ['created_at', 'location', 'opening_time', 'closing_time'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // التصفح
        $perPage = $request->input('per_page', 10);
        $clinics = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'تم جلب العيادات بنجاح',
            'data' => ClinicResource::collection($clinics),
            'meta' => [
                'current_page' => $clinics->currentPage(),
                'from' => $clinics->firstItem(),
                'last_page' => $clinics->lastPage(),
                'per_page' => $clinics->perPage(),
                'to' => $clinics->lastItem(),
                'total' => $clinics->total(),
                'has_more_pages' => $clinics->hasMorePages(),
            ],
            'links' => [
                'first' => $clinics->url(1),
                'last' => $clinics->url($clinics->lastPage()),
                'prev' => $clinics->previousPageUrl(),
                'next' => $clinics->nextPageUrl(),
            ],
            'filters' => [
                'major_id' => $request->major_id,
                'location' => $request->location,
                'time' => $request->time,
                'name' => $request->name,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'per_page' => $perPage,
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء جلب العيادات',
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function store(Request $request)
    {
        try {
            $validated = $this->validateClinic($request);

            // Handle photos upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('clinics', 'public');
                $validated['photo'] = $photoPath;
            }

            if ($request->hasFile('cover_photo')) {
                $coverPhotoPath = $request->file('cover_photo')->store('clinics/covers', 'public');
                $validated['cover_photo'] = $coverPhotoPath;
            }

            $clinic = NClinic::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'تم إنشاء العيادة بنجاح',
                'data' => new ClinicResource($clinic)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(NClinic $clinic)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => new ClinicResource($clinic->load(['user', 'major', 'doctors']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, NClinic $clinic)
    {
        try {
            $validated = $this->validateClinic($request, true);

            // Handle photos update
            if ($request->hasFile('photo')) {
                if ($clinic->photo) {
                    Storage::disk('public')->delete($clinic->photo);
                }
                $photoPath = $request->file('photo')->store('clinics', 'public');
                $validated['photo'] = $photoPath;
            }

            if ($request->hasFile('cover_photo')) {
                if ($clinic->cover_photo) {
                    Storage::disk('public')->delete($clinic->cover_photo);
                }
                $coverPhotoPath = $request->file('cover_photo')->store('clinics/covers', 'public');
                $validated['cover_photo'] = $coverPhotoPath;
            }

            // Check if working hours update affects doctors
            if (($request->has('opening_time') && $request->opening_time != $clinic->opening_time) ||
                ($request->has('closing_time') && $request->closing_time != $clinic->closing_time)) {
                
                $conflictingDoctors = $clinic->doctors()
                    ->where(function($query) use ($validated) {
                        $query->where('start_work_time', '<', $validated['opening_time'])
                              ->orWhere('end_work_time', '>', $validated['closing_time']);
                    })->exists();

                if ($conflictingDoctors) {
                    return response()->json([
                        'status' => false,
                        'message' => 'لا يمكن تغيير ساعات العمل. يوجد أطباء خارج هذه الساعات'
                    ], 422);
                }
            }

            $clinic->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث العيادة بنجاح',
                'data' => new ClinicResource($clinic)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(NClinic $clinic)
    {
        try {
            if ($clinic->doctors()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'لا يمكن حذف العيادة. يوجد أطباء مرتبطين بها'
                ], 422);
            }

            // Delete photos
            if ($clinic->photo) {
                Storage::disk('public')->delete($clinic->photo);
            }
            if ($clinic->cover_photo) {
                Storage::disk('public')->delete($clinic->cover_photo);
            }

            $clinic->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف العيادة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDoctors(NClinic $clinic)
    {
        try {
            $doctors = $clinic->doctors()->with('user')->get();
            return response()->json([
                'status' => true,
                'data' => DoctorResource::collection($doctors)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}