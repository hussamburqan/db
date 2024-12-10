<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use App\Http\Resources\PatientResource;

class PatientController extends Controller
{
   private function validatePatient(Request $request, $isUpdate = false)
   {
       return $request->validate([
           'medical_history' => 'required|string',
           'allergies' => 'required|string',
           'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
           'current_medications' => 'required|string',
           'medical_recommendations' => 'required|string',
       ]);
   }

   public function index(Request $request)
   {
       try {
           $query = Patient::with(['user']);

           if ($request->has('name')) {
               $query->whereHas('user', function($q) use ($request) {
                   $q->where('name', 'like', '%' . $request->name . '%');
               });
           }

           if ($request->has('blood_type')) {
               $query->where('blood_type', $request->blood_type);
           }

           $patients = $query->paginate(10);

           return response()->json([
               'status' => true,
               'data' => PatientResource::collection($patients)
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
        \Log::info('User Validated:', $userValidated);
    
        $userValidated['password'] = bcrypt($userValidated['password']);
        $user = User::create($userValidated);
    
        if (!$user) {
            throw new \Exception('User creation failed.');
        }
    
        $patientValidated = $this->validatePatient($request);
        \Log::info('Patient Validated:', $patientValidated);
    
        $patientValidated['user_id'] = $user->id;
        $patient = Patient::create($patientValidated);
    
        if (!$patient) {
            throw new \Exception('Patient creation failed.');
        }
    
        DB::commit();
    
        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء حساب المريض بنجاح',
            'data' => new PatientResource($patient)
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error:', ['message' => $e->getMessage()]);
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
    
   }

   public function show(Patient $patient)
   {
       try {
           return response()->json([
               'status' => true,
               'data' => new PatientResource($patient->load([
                   'user',
                   'archives.doctor.user',
                   'archives.doctor.clinic',
                   'diseases.doctor.user',
                   'reservations.doctor.user',
                   'reservations.clinic'
               ]))
           ]);
       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function update(Request $request, Patient $patient)
   {
       try {
           DB::beginTransaction();

           if ($request->has('name') || $request->has('email') || $request->has('address') || 
               $request->has('age') || $request->has('gender') || $request->has('phone')) {
               
               $userValidated = $request->validate([
                   'name' => 'string|max:255',
                   'email' => 'email|unique:users,email,' . $patient->user_id,
                   'address' => 'string',
                   'age' => 'integer|between:1,120',
                   'gender' => 'in:male,female',
                   'phone' => 'string'
               ]);

               $patient->user()->update($userValidated);
           }

           $patientValidated = $this->validatePatient($request, true);
           $patient->update($patientValidated);

           DB::commit();

           return response()->json([
               'status' => true,
               'message' => 'تم تحديث بيانات المريض بنجاح',
               'data' => new PatientResource($patient->fresh(['user']))
           ]);
       } catch (\Exception $e) {
           DB::rollBack();
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function destroy(Patient $patient)
   {
       try {
           if ($patient->archives()->exists()) {
               return response()->json([
                   'status' => false,
                   'message' => 'لا يمكن حذف مريض لديه سجل طبي'
               ], 422);
           }

           DB::beginTransaction();
           
           $patient->user->delete();
           
           DB::commit();

           return response()->json([
               'status' => true,
               'message' => 'تم حذف المريض بنجاح'
           ]);
       } catch (\Exception $e) {
           DB::rollBack();
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function getMedicalHistory(Patient $patient)
   {
       try {
           $archives = $patient->archives()
                             ->with(['doctor.user', 'doctor.clinic'])
                             ->orderBy('date', 'desc')
                             ->get();

           $diseases = $patient->diseases()
                             ->with('doctor.user')
                             ->get();

           return response()->json([
               'status' => true,
               'data' => [
                   'patient' => new PatientResource($patient->load('user')),
                   'archives' => PatientArchiveResource::collection($archives),
                   'diseases' => DiseaseResource::collection($diseases)
               ]
           ]);
       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function updateMedicalRecommendations(Request $request, Patient $patient)
   {
       try {
           $validated = $request->validate([
               'medical_recommendations' => 'required|string'
           ]);

           $patient->update($validated);

           return response()->json([
               'status' => true,
               'message' => 'تم تحديث التوصيات الطبية بنجاح',
               'data' => new PatientResource($patient)
           ]);
       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function updateCurrentMedications(Request $request, Patient $patient)
   {
       try {
           $validated = $request->validate([
               'current_medications' => 'required|string'
           ]);

           $patient->update($validated);

           return response()->json([
               'status' => true,
               'message' => 'تم تحديث الأدوية الحالية بنجاح',
               'data' => new PatientResource($patient)
           ]);
       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }
}