<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function dashboard(Request $request)
    {
        try {
            // Statistiques de base
            $totalUsers = User::count();
            $totalProducts = Product::count();
            $totalOrders = Order::count();
            
            // Chiffre d'affaires : inclure toutes les commandes payées, expédiées et livrées
            $totalRevenue = Order::whereIn('status', ['paid', 'shipped', 'delivered'])
                ->sum('total_amount');
            
            $pendingOrders = Order::where('status', 'pending')->count();
            
            // Commandes récentes
            $recentOrders = Order::with(['items.product'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'customer_name' => $order->customer_name,
                        'total_amount' => $order->total_amount,
                        'status' => $order->status,
                        'created_at' => $order->created_at
                    ];
                });
            
            // Produits en stock faible
            $lowStockProducts = DB::table('product_size')
                ->join('products', 'products.id', '=', 'product_size.product_id')
                ->join('sizes', 'sizes.id', '=', 'product_size.size_id')
                ->where('product_size.stock', '<', 5)
                ->where('product_size.stock', '>', 0)
                ->select(
                    'products.id',
                    'products.name as product_name',
                    'sizes.name as size_name',
                    'product_size.stock'
                )
                ->get();
            
            // Nouveaux utilisateurs aujourd'hui
            $newUsersToday = User::whereDate('created_at', Carbon::today())->count();
            
            // Commandes aujourd'hui
            $ordersToday = Order::whereDate('created_at', Carbon::today())->count();
            
            // Revenus mensuels
            $monthlyRevenue = $this->getMonthlyRevenue();
            
            // Commandes par statut
            $ordersByStatus = Order::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
            
            // Chiffre d'affaires aujourd'hui
            $revenueToday = Order::whereIn('status', ['paid', 'shipped', 'delivered'])
                ->whereDate('created_at', Carbon::today())
                ->sum('total_amount');
            
            // Chiffre d'affaires cette semaine
            $revenueThisWeek = Order::whereIn('status', ['paid', 'shipped', 'delivered'])
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->sum('total_amount');
            
            // Chiffre d'affaires ce mois
            $revenueThisMonth = Order::whereIn('status', ['paid', 'shipped', 'delivered'])
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_amount');
            
            $stats = [
                'total_users' => $totalUsers,
                'total_products' => $totalProducts,
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'revenue_today' => $revenueToday,
                'revenue_this_week' => $revenueThisWeek,
                'revenue_this_month' => $revenueThisMonth,
                'pending_orders' => $pendingOrders,
                'recent_orders' => $recentOrders,
                'low_stock_products' => $lowStockProducts,
                'new_users_today' => $newUsersToday,
                'orders_today' => $ordersToday,
                'monthly_revenue' => $monthlyRevenue,
                'orders_by_status' => $ordersByStatus
            ];
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Erreur lors du chargement des statistiques',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get monthly revenue for the current year
     */
    private function getMonthlyRevenue()
    {
        try {
            $currentYear = Carbon::now()->year;
            $monthlyRevenue = [];
            
            for ($month = 1; $month <= 12; $month++) {
                $revenue = Order::whereIn('status', ['paid', 'shipped', 'delivered'])
                    ->whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $month)
                    ->sum('total_amount');
                
                $monthlyRevenue[] = [
                    'month' => $month,
                    'year' => $currentYear,
                    'total' => $revenue ?: 0
                ];
            }
            
            return $monthlyRevenue;
            
        } catch (\Exception $e) {
            Log::error('Monthly revenue error: ' . $e->getMessage());
            return [];
        }
    }
}
