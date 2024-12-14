<?php

namespace App\Http\Controllers;


use App\Models\NClinic;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;  
use App\Http\Resources\ClinicResource;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Auth; 
use App\Models\Reservation; 
use App\Models\Doctor; 

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

        if ($request->has('name')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSortFields = ['created_at', 'location', 'opening_time', 'closing_time'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

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

    public function showRegisterForm()
    {
        $majors = Major::all();
        return view('clinic.register', compact('majors'));
    }

    public function showLoginForm()
    {
        return view('clinic.login');
    }

    public function register(Request $request)
    {
        try {
            DB::beginTransaction();

            $userValidated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'address' => 'required|string',
                'age' => 'required|integer|between:1,120',
                'gender' => 'required|in:male,female',
                'phone' => 'required|string'
            ]);

            $user = User::create([
                'name' => $userValidated['name'],
                'email' => $userValidated['email'],
                'password' => Hash::make($userValidated['password']),
                'address' => $userValidated['address'],
                'age' => $userValidated['age'],
                'gender' => $userValidated['gender'],
                'phone' => $userValidated['phone']
            ]);

            $clinicValidated = $request->validate([
                'location' => 'required|string',
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'cover_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'description' => 'required|string',
                'opening_time' => 'required|date_format:H:i',
                'closing_time' => 'required|date_format:H:i',
                'major_id' => 'required|exists:majors,id'
            ]);

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('clinics', 'public');
                $clinicValidated['photo'] = $photoPath;
            }

            if ($request->hasFile('cover_photo')) {
                $coverPhotoPath = $request->file('cover_photo')->store('clinics/covers', 'public');
                $clinicValidated['cover_photo'] = $coverPhotoPath;
            }

            $clinic = NClinic::create([
                'user_id' => $user->id,
                'location' => $clinicValidated['location'],
                'photo' => $clinicValidated['photo'],
                'cover_photo' => $clinicValidated['cover_photo'],
                'description' => $clinicValidated['description'],
                'opening_time' => $clinicValidated['opening_time'],
                'closing_time' => $clinicValidated['closing_time'],
                'major_id' => $clinicValidated['major_id'],
            ]);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'تم تسجيل العيادة بنجاح',
                    'data' => $clinic
                ], 201);
            }

            return redirect()->route('clinic.login')
                           ->with('success', 'تم تسجيل العيادة بنجاح');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Clinic registration error:', ['error' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'حدث خطأ أثناء التسجيل'
                ], 500);
            }

            return back()->withErrors(['error' => 'حدث خطأ أثناء التسجيل'])->withInput();
        }
    }

    public function dashboard()
    {
        try {
            $clinic = NClinic::where('user_id', auth()->id())->first();
            
            $pendingReservations = Reservation::where('nclinic_id', $clinic->id)
                ->where('status', 'pending')
                ->orderBy('date', 'asc')
                ->get();
                
            $todayReservations = Reservation::where('nclinic_id', $clinic->id)
                ->where('status', 'accepted')
                ->whereDate('date', today())
                ->orderBy('time', 'asc')
                ->get();
                
            $doctors = Doctor::where('nclinic_id', $clinic->id)->get();
            
            return view('clinic.dashboard', compact('clinic', 'pendingReservations', 'todayReservations', 'doctors'));
        } catch (\Exception $e) {
            return redirect()->route('clinic.login.form')
                           ->with('error', 'حدث خطأ أثناء تحميل لوحة التحكم');
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
    
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $clinic = NClinic::where('user_id', $user->id)->first();
    
                if (!$clinic) {
                    throw new \Exception('لم يتم العثور على عيادة مرتبطة بهذا الحساب');
                }
    
                if ($request->wantsJson()) {
                    $token = $user->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        'status' => true,
                        'message' => 'تم تسجيل الدخول بنجاح',
                        'data' => $clinic,
                        'access_token' => $token,
                        'token_type' => 'Bearer'
                    ]);
                }
    
                return redirect()->route('clinic.dashboard')
                               ->with('success', 'تم تسجيل الدخول بنجاح');
            }
    
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'بيانات الدخول غير صحيحة'
                ], 401);
            }
    
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'بيانات الدخول غير صحيحة']);
    
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
    
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['error' => $e->getMessage()]);
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