<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return response()->json($categories);
    }
    
    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $category = Category::create($request->all());
        
        return response()->json($category, 201);
    }
    
    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $category->update($request->all());
        
        return response()->json($category);
    }
    
    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        // Vérifier s'il y a des produits associés
        if ($category->products()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer une catégorie qui contient des produits.'], 422);
        }
        
        $category->delete();
        
        return response()->json(['message' => 'Catégorie supprimée avec succès.']);
    }
}
