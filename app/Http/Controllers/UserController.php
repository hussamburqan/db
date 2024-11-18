<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Traits\MobileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use MobileResponse;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'address' => 'required|string',
            'age' => 'required|integer',
            'gender' => 'required|string|in:male,female',
            'blood_type' => 'required|string|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'age' => $request->age,
            'gender' => $request->gender,
            'blood_type' => $request->blood_type,
        ]);

        return $this->success(new UserResource($user));
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->fail('Invalid credentials', 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success('Logged out successfully');
    }

    public function all()
    {
        $users = User::all();
        return $this->success(UserResource::collection($users));
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->fail('User not found', 404);
        }
        return $this->success(new UserResource($user));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->fail('User not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'address' => 'sometimes|string',
            'age' => 'sometimes|integer',
            'gender' => 'sometimes|string|in:male,female',
            'blood_type' => 'sometimes|string|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        $updateData = array_filter([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'age' => $request->age,
            'gender' => $request->gender,
            'blood_type' => $request->blood_type,
        ]);

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return $this->success(new UserResource($user));
    }

    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->fail('User not found', 404);
        }

        $user->delete();
        return $this->success('User deleted successfully');
    }

    public function profile(Request $request)
    {
        return $this->success(new UserResource($request->user()));
    }
}