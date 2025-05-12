<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Size;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();
        $products = Product::with('sizes')->get();
        
        if ($products->isEmpty()) {
            $this->command->info('Aucun produit trouvé. Veuillez créer des produits d\'abord.');
            return;
        }
        
        // Créer des commandes pour les derniers mois
        for ($i = 0; $i < 20; $i++) {
            $date = Carbon::now()->subDays(rand(0, 90));
            $status = $this->randomStatus();
            
            $order = Order::create([
                'user_id' => $users->isNotEmpty() ? $users->random()->id : null,
                'customer_name' => 'Client ' . ($i + 1),
                'customer_email' => 'client' . ($i + 1) . '@example.com',
                'customer_phone' => '06' . rand(10000000, 99999999),
                'customer_address' => rand(1, 100) . ' rue Example, 75000 Paris',
                'status' => $status,
                'is_live_order' => rand(0, 1),
                'notes' => 'Commande de test ' . ($i + 1),
                'total_amount' => 0,
                'payment_date' => $status === 'paid' ? $date->copy()->addHours(rand(1, 24)) : null,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            
            // Ajouter des articles à la commande
            $totalAmount = 0;
            $itemsCount = rand(1, 4);
            
            for ($j = 0; $j < $itemsCount; $j++) {
                $product = $products->random();
                $size = $product->sizes->isNotEmpty() ? $product->sizes->random() : null;
                
                if ($size) {
                    $quantity = rand(1, 3);
                    $price = $product->price;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'size_id' => $size->id,
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);
                    
                    $totalAmount += $price * $quantity;
                }
            }
            
            // Mettre à jour le montant total
            $order->update(['total_amount' => $totalAmount]);
        }
        
        $this->command->info('20 commandes de test ont été créées.');
    }
    
    private function randomStatus()
    {
        $statuses = ['pending', 'confirmed', 'paid', 'shipped', 'delivered', 'cancelled'];
        return $statuses[array_rand($statuses)];
    }
}
