<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index()
    {
        $majors = Major::all();
        return response()->json($majors);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $major = Major::create($validated);
        return response()->json($major, 201);
    }

    public function show(Major $major)
    {
        return response()->json($major);
    }

    public function update(Request $request, Major $major)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
        ]);

        $major->update($validated);
        return response()->json($major);
    }

    public function destroy(Major $major)
    {
        $major->delete();
        return response()->json(null, 204);
    }
}