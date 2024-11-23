<?php

namespace App\Http\Controllers;

use App\Models\NClinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NClinicsController extends Controller
{
    private function validateClinic(Request $request, $isUpdate = false)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'location' => 'required|string',
            'description' => 'required|string',
            'opening_time' => ['required', 'date_format:H:i', function($attribute, $value, $fail) use ($request) {
                if ($request->filled('closing_time') && $value >= $request->closing_time) {
                    $fail('Opening time must be before closing time.');
                }
            }],
            'closing_time' => ['required', 'date_format:H:i', function($attribute, $value, $fail) use ($request) {
                if ($request->filled('opening_time') && $value <= $request->opening_time) {
                    $fail('Closing time must be after opening time.');
                }
            }],
            'status' => 'required|in:active,inactive,maintenance',
            'email' => 'required|email',
            'phone' => [
                'required',
                'string',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'min:10'
            ],
            'major_id' => 'required|exists:majors,id',
            'photo' => $isUpdate ? 'nullable|image|mimes:jpeg,png,jpg|max:2048' : 'required|image|mimes:jpeg,png,jpg|max:2048'

        ];

        return $request->validate($rules);
    }

    public function index()
    {
        try {
            $clinics = NClinic::with(['major', 'doctors'])->paginate(10);
            
            return response()->json([
                'status' => true,
                'message' => 'Clinics retrieved successfully',
                'data' => $clinics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error retrieving clinics',
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

            $clinic = NClinic::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Clinic created successfully',
                'data' => $clinic->load('major')
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creating clinic',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(NClinic $clinic)
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'Clinic retrieved successfully',
                'data' => $clinic->load(['major', 'doctors'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error retrieving clinic',
                'error' => $e->getMessage()
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

            $overlappingClinic = NClinic::where('major_id', $validated['major_id'])
                ->where('id', '!=', $clinic->id)
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('opening_time', [$validated['opening_time'], $validated['closing_time']])
                        ->orWhereBetween('closing_time', [$validated['opening_time'], $validated['closing_time']]);
                })->first();

            if ($overlappingClinic) {
                if (isset($validated['photo'])) {
                    Storage::disk('public')->delete($validated['photo']);
                }
                
                return response()->json([
                    'status' => false,
                    'message' => 'Working hours overlap with another clinic in the same major'
                ], 422);
            }

            $clinic->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Clinic updated successfully',
                'data' => $clinic->fresh()->load('major')
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
                'message' => 'Error updating clinic',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(NClinic $clinic)
    {
        try {
            if ($clinic->appointments()->where('status', 'scheduled')->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete clinic with active appointments'
                ], 422);
            }

            if ($clinic->photo) {
                Storage::disk('public')->delete($clinic->photo);
            }

            $clinic->delete();

            return response()->json([
                'status' => true,
                'message' => 'Clinic deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting clinic',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function search(Request $request)
    {
        try {
            $query = NClinic::query()->with(['major', 'doctors']);

            if ($request->has('name')) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }

            if ($request->has('location')) {
                $query->where('location', 'like', '%' . $request->location . '%');
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('major_id')) {
                $query->where('major_id', $request->major_id);
            }

            $clinics = $query->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'Search results retrieved successfully',
                'data' => $clinics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error searching clinics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}