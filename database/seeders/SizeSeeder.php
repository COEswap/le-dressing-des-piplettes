<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            ['name' => 'XS', 'description' => 'Extra Small'],
            ['name' => 'S', 'description' => 'Small'],
            ['name' => 'M', 'description' => 'Medium'],
            ['name' => 'L', 'description' => 'Large'],
            ['name' => 'XL', 'description' => 'Extra Large'],
            ['name' => 'XXL', 'description' => 'Double Extra Large'],
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
}
