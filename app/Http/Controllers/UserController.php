<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private function validateUser(Request $request, $isUpdate = false)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => $isUpdate ? 'required|email|unique:users,email,' . $request->user->id : 'required|email|unique:users',
            'password' => $isUpdate ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
            'address' => 'required|string',
            'age' => 'required|integer|between:1,120',
            'gender' => 'required|in:male,female,other',
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
        ];

        return $request->validate($rules);
    }

    public function register(Request $request)
    {
        try {
            $validated = $this->validateUser($request);
            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'status' => true,
                'message' => 'Registration successful',
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Error:', $e->errors()); 
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
    
            if (!Auth::attempt($validated)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
    
            $user = User::where('email', $validated['email'])->first();
    
            $user->load('patient'); 
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    

    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function profile()
    {
        try {
            $user = auth()->user()->load(['doctor', 'patient']);
            return response()->json([
                'status' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $users = User::with(['doctor', 'patient'])->paginate(10);
            return response()->json([
                'status' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(User $user)
    {
        try {
            return response()->json([
                'status' => true,
                'data' => $user->load(['doctor', 'patient','clinic'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $this->validateUser($request, true);
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }
            $user->update($validated);
            return response()->json([
                'status' => true,
                'data' => $user
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed'
            ]);

            $user = auth()->user();

            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Current password is incorrect'
                ], 401);
            }

            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Password updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}



