<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Robes', 'description' => 'Robes élégantes pour toutes occasions'],
            ['name' => 'Hauts', 'description' => 'T-shirts, chemises et blouses'],
            ['name' => 'Bas', 'description' => 'Pantalons, jupes et shorts'],
            ['name' => 'Accessoires', 'description' => 'Accessoires de mode'],
            ['name' => 'Vestes & Manteaux', 'description' => 'Vestes, manteaux et vêtements d\'extérieur'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
