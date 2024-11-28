<?php

namespace App\Http\Controllers;

use App\Models\MedicalNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalNewsController extends Controller 
{
   private function validateNews(Request $request, $isUpdate = false)
   {
       return $request->validate([
           'title' => 'required|string|max:255',
           'content' => 'required|string', 
           'image' => $isUpdate ? 'nullable|image|mimes:jpeg,png,jpg|max:2048' : 'required|image|mimes:jpeg,png,jpg|max:2048',
           'category' => 'required|string',
           'is_featured' => 'boolean',
           'is_active' => 'boolean'
       ]);
   }
   
   public function index(Request $request)
   {
       try {
           $query = MedicalNews::query();

           if ($request->has('category')) {
               $query->where('category', $request->category);
           }

           if ($request->has('is_active')) {
               $query->where('is_active', $request->is_active); 
           }

           if ($request->has('is_featured')) {
               $query->where('is_featured', $request->is_featured);
           }

           if ($request->has('search')) {
               $query->where('title', 'like', '%' . $request->search . '%');
           }

           $news = $query->latest()->paginate(10);

           return response()->json([
               'status' => true,
               'data' => $news
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
           $validated = $this->validateNews($request);

           if ($request->hasFile('image')) {
               $imagePath = $request->file('image')->store('news', 'public');
               $validated['image'] = $imagePath;
           }

           $news = MedicalNews::create($validated);

           return response()->json([
               'status' => true,
               'message' => 'تم إنشاء الخبر بنجاح',
               'data' => $news
           ], 201);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false, 
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function show(MedicalNews $news)
   {
       try {
           if (!$news->is_active) {
               return response()->json([
                   'status' => false,
                   'message' => 'هذا الخبر غير متاح'
               ], 404);
           }

           return response()->json([
               'status' => true,
               'data' => $news
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function update(Request $request, MedicalNews $news)
   {
       try {
           $validated = $this->validateNews($request, true);

           if ($request->hasFile('image')) {
               if ($news->image) {
                   Storage::disk('public')->delete($news->image);
               }
               $imagePath = $request->file('image')->store('news', 'public');
               $validated['image'] = $imagePath;
           }

           $news->update($validated);

           return response()->json([
               'status' => true,
               'message' => 'تم تحديث الخبر بنجاح',
               'data' => $news
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function destroy(MedicalNews $news)
   {
       try {
           if ($news->image) {
               Storage::disk('public')->delete($news->image);
           }

           $news->delete();

           return response()->json([
               'status' => true,
               'message' => 'تم حذف الخبر بنجاح'
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function getFeatured()
   {
       try {
           $featuredNews = MedicalNews::where('is_featured', true)
               ->where('is_active', true)
               ->latest()
               ->take(5)
               ->get();

           return response()->json([
               'status' => true,
               'data' => $featuredNews
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500); 
       }
   }

   public function toggleStatus(MedicalNews $news)
   {
       try {
           $news->update(['is_active' => !$news->is_active]);

           return response()->json([
               'status' => true,
               'message' => 'تم تحديث حالة الخبر بنجاح',
               'data' => $news
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }

   public function toggleFeatured(MedicalNews $news)
   {
       try {
           $news->update(['is_featured' => !$news->is_featured]);

           return response()->json([
               'status' => true, 
               'message' => 'تم تحديث حالة التمييز بنجاح',
               'data' => $news
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ], 500);
       }
   }
}