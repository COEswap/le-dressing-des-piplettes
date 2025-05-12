<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Afficher une liste des commandes pour l'administration.
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'items.size']);
        
        // Filtrage par recherche
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par statut
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filtrage par type de commande
        if ($request->has('is_live_order') && $request->is_live_order != '') {
            $query->where('is_live_order', $request->is_live_order === 'true');
        }
        
        // Filtrage par date
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return response()->json($orders);
    }

    /**
     * Afficher les détails d'une commande pour l'administration.
     */
    public function adminShow(Order $order)
    {
        return response()->json($order->load(['user', 'items.product', 'items.size']));
    }

    /**
     * Mettre à jour une commande pour l'administration.
     */
    public function adminUpdate(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'string|in:pending,confirmed,paid,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
            'payment_date' => 'nullable|date',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $order->update($request->all());
        
        return response()->json($order->load(['user', 'items.product', 'items.size']));
    }

    /**
     * Afficher une liste des commandes.
     */
    public function index(Request $request)
    {
        // Si l'utilisateur est connecté, montrer uniquement ses commandes
        if ($request->user()) {
            $orders = $request->user()->orders()->with(['items.product', 'items.size'])->get();
        } else {
            // Si c'est un admin (à implémenter avec les rôles)
            $orders = Order::with(['items.product', 'items.size'])->get();
        }

        return response()->json($orders);
    }

    /**
     * Stocker une nouvelle commande.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'status' => 'string|in:pending,confirmed,paid,shipped,delivered,cancelled',
            'is_live_order' => 'boolean',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.size_id' => 'required|exists:sizes,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;

            // Calculer le montant total et vérifier le stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Vérifier si le produit a cette taille
                $pivotRecord = DB::table('product_size')
                    ->where('product_id', $product->id)
                    ->where('size_id', $item['size_id'])
                    ->first();

                if (!$pivotRecord) {
                    throw new \Exception("Le produit {$product->name} n'est pas disponible dans la taille sélectionnée.");
                }

                // Vérifier le stock
                if ($pivotRecord->stock < $item['quantity']) {
                    throw new \Exception("Stock insuffisant pour le produit {$product->name}.");
                }

                // Réduire le stock
                DB::table('product_size')
                    ->where('product_id', $product->id)
                    ->where('size_id', $item['size_id'])
                    ->decrement('stock', $item['quantity']);

                $totalAmount += $product->price * $item['quantity'];
            }

            // Préparation des données de la commande
            $orderData = [
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'status' => $request->status ?? 'pending',
                'is_live_order' => $request->is_live_order ?? false,
                'notes' => $request->notes,
                'total_amount' => $totalAmount,
            ];

            // Si l'utilisateur est connecté, associer la commande à son compte
            if ($request->user()) {
                $orderData['user_id'] = $request->user()->id;
            }

            // Créer la commande
            $order = Order::create($orderData);

            // Créer les éléments de la commande
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'size_id' => $item['size_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            DB::commit();
            return response()->json($order->load(['items.product', 'items.size']), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Afficher la commande spécifiée.
     */
    public function show(Request $request, Order $order)
    {
        // Vérifier si l'utilisateur a le droit de voir cette commande
        if ($request->user() && $request->user()->id !== $order->user_id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json($order->load(['items.product', 'items.size']));
    }

    /**
     * Mettre à jour la commande spécifiée.
     */
    public function update(Request $request, Order $order)
    {
        // Vérifier si l'utilisateur a le droit de modifier cette commande
        if ($request->user() && $request->user()->id !== $order->user_id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'customer_name' => 'string|max:255',
            'customer_email' => 'email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'status' => 'string|in:pending,confirmed,paid,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
            'payment_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order->update($request->all());

        return response()->json($order->load(['items.product', 'items.size']));
    }

    /**
     * Supprimer la commande spécifiée.
     */
    public function destroy(Request $request, Order $order)
    {
        // Vérifier si l'utilisateur a le droit de supprimer cette commande
        if ($request->user() && $request->user()->id !== $order->user_id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        try {
            DB::beginTransaction();

            // Remettre les produits en stock
            foreach ($order->items as $item) {
                DB::table('product_size')
                    ->where('product_id', $item->product_id)
                    ->where('size_id', $item->size_id)
                    ->increment('stock', $item->quantity);
            }

            // Supprimer la commande (les éléments seront supprimés en cascade)
            $order->delete();

            DB::commit();
            return response()->json(null, 204);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Créer une commande en direct depuis le live Facebook.
     */
    public function createLiveOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.size_id' => 'required|exists:sizes,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Marquer la commande comme étant une commande en direct
        $request->merge(['is_live_order' => true]);

        return $this->store($request);
    }
}
