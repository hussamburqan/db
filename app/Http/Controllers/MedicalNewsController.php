<?php

namespace App\Http\Controllers;

use App\Models\MedicalNews;
use Illuminate\Http\Request;

class MedicalNewsController extends Controller {
    
    private function validateNews(Request $request) {

        return $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'category' => 'required|string',
            'is_featured' => 'boolean',
            'is_active' => 'boolean'
        ]);
    }
 
    public function index() {
        $news = MedicalNews::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return response()->json([
            'status' => true,
            'data' => $news
        ]);
    }
 
    public function store(Request $request) {
        try {
            $validated = $this->validateNews($request);
            
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('news', 'public');
                $validated['image'] = $imagePath;
            }
 
            $news = MedicalNews::create($validated);
            
            return response()->json([
                'status' => true,
                'message' => 'News created successfully',
                'data' => $news
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id)
{
    $news = MedicalNews::findOrFail($id);
    $news->update($request->all());
    return response()->json(['message' => 'Medical news updated successfully!', 'news' => $news]);
}

public function destroy($id)
{
    $news = MedicalNews::findOrFail($id);
    $news->delete();
    return response()->json(['message' => 'Medical news deleted successfully!']);
}

}