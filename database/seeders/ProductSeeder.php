<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Size;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer toutes les tailles
        $sizes = Size::all();
        
        // Créer quelques produits
        $products = [
            [
                'name' => 'Robe d\'été fleurie',
                'description' => 'Robe légère à motifs floraux, parfaite pour l\'été',
                'price' => 39.99,
                'image_url' => 'https://picsum.photos/id/21/500/500',
                'reference' => 'ROBE-FLEUR-001',
                'category_id' => 1,
                'is_live_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Blouse blanche en lin',
                'description' => 'Blouse élégante en lin naturel',
                'price' => 29.99,
                'image_url' => 'https://picsum.photos/id/22/500/500',
                'reference' => 'BLOUSE-LIN-001',
                'category_id' => 2,
                'is_live_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Pantalon taille haute',
                'description' => 'Pantalon élégant à taille haute',
                'price' => 45.99,
                'image_url' => 'https://picsum.photos/id/23/500/500',
                'reference' => 'PANT-HW-001',
                'category_id' => 3,
                'is_live_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Veste en jean oversize',
                'description' => 'Veste en jean décontractée style oversize',
                'price' => 59.99,
                'image_url' => 'https://picsum.photos/id/24/500/500',
                'reference' => 'VESTE-JEAN-001',
                'category_id' => 5,
                'is_live_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Écharpe en cachemire',
                'description' => 'Écharpe douce et élégante en cachemire',
                'price' => 25.99,
                'image_url' => 'https://picsum.photos/id/25/500/500',
                'reference' => 'ACC-ECHARPE-001',
                'category_id' => 4,
                'is_live_available' => true,
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            
            // Ajouter les tailles S, M et L avec des stocks variés
            $sizeS = $sizes->where('name', 'S')->first();
            $sizeM = $sizes->where('name', 'M')->first();
            $sizeL = $sizes->where('name', 'L')->first();
            
            if ($sizeS) {
                $product->sizes()->attach($sizeS->id, ['stock' => rand(0, 10)]);
            }
            if ($sizeM) {
                $product->sizes()->attach($sizeM->id, ['stock' => rand(0, 10)]);
            }
            if ($sizeL) {
                $product->sizes()->attach($sizeL->id, ['stock' => rand(0, 10)]);
            }
        }
    }
}
