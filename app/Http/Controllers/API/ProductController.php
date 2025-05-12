<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Afficher une liste des produits pour l'administration.
     */
    public function adminIndex(Request $request)
    {
        $query = Product::with(['category', 'sizes']);
        
        // Filtrage par recherche
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par catégorie
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        
        // Filtrage par disponibilité
        if ($request->has('is_active') && $request->is_active != '') {
            $query->where('is_active', $request->is_active === 'true');
        }
        
        if ($request->has('is_live_available') && $request->is_live_available != '') {
            $query->where('is_live_available', $request->is_live_available === 'true');
        }
        
        // Filtrage par stock
        if ($request->has('stock_status') && $request->stock_status != '') {
            if ($request->stock_status === 'low') {
                $query->whereHas('sizes', function ($q) {
                    $q->where('stock', '<', 5)
                      ->where('stock', '>', 0);
                });
            } elseif ($request->stock_status === 'out') {
                $query->whereHas('sizes', function ($q) {
                    $q->where('stock', 0);
                });
            }
        }
        
        $products = $query->paginate(10);
        
        return response()->json($products);
    }
    
    /**
     * Afficher une liste des produits.
     */
    public function index()
    {
        $products = Product::with(['category', 'sizes'])
            ->where('is_active', true)
            ->get();
        
        return response()->json($products);
    }
    
    /**
     * Stocker un nouveau produit.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|string|max:255',
            'reference' => 'required|string|unique:products,reference',
            'category_id' => 'nullable|exists:categories,id',
            'is_live_available' => 'boolean',
            'is_active' => 'boolean',
            'sizes' => 'array',
            'sizes.*.id' => 'required|exists:sizes,id',
            'sizes.*.stock' => 'required|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $product = Product::create($request->except('sizes'));
        
        // Associer les tailles avec leur stock
        if ($request->has('sizes')) {
            foreach ($request->sizes as $size) {
                $product->sizes()->attach($size['id'], ['stock' => $size['stock']]);
            }
        }
        
        return response()->json($product->load(['category', 'sizes']), 201);
    }
    
    /**
     * Afficher le produit spécifié.
     */
    public function show(Product $product)
    {
        return response()->json($product->load(['category', 'sizes']));
    }
    
    /**
     * Mettre à jour le produit spécifié.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'image_url' => 'nullable|string|max:255',
            'reference' => 'string|unique:products,reference,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'is_live_available' => 'boolean',
            'is_active' => 'boolean',
            'sizes' => 'array',
            'sizes.*.id' => 'required|exists:sizes,id',
            'sizes.*.stock' => 'required|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $product->update($request->except('sizes'));
        
        // Mettre à jour les tailles et leur stock
        if ($request->has('sizes')) {
            $sizesData = [];
            foreach ($request->sizes as $size) {
                $sizesData[$size['id']] = ['stock' => $size['stock']];
            }
            $product->sizes()->sync($sizesData);
        }
        
        return response()->json($product->load(['category', 'sizes']));
    }
    
    /**
     * Supprimer le produit spécifié.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        
        return response()->json(null, 204);
    }
    
    /**
     * Obtenir les produits disponibles en live.
     */
    public function getLiveProducts()
    {
        $products = Product::with(['category', 'sizes'])
            ->where('is_live_available', true)
            ->where('is_active', true)
            ->get();
        
        return response()->json($products);
    }
    
    /**
     * Mettre à jour le stock d'un produit.
     */
    public function updateStock(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'size_id' => 'required|exists:sizes,id',
            'stock' => 'required|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $product->sizes()->updateExistingPivot($request->size_id, [
            'stock' => $request->stock
        ]);
        
        return response()->json($product->load(['category', 'sizes']));
    }
}