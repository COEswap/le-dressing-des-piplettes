Création d'un site e-commerce pour "Le dressing des piplettes"
Bonjour ! Je comprends votre besoin de créer un site e-commerce pour votre marque "Le dressing des piplettes" qui vous permettra de mieux gérer vos ventes en direct sur Facebook.
Première étape : Configuration du projet Laravel
Commençons par créer un nouveau projet Laravel 12. Voici comment procéder :
Ouvrez votre terminal ou invite de commande
Naviguez vers le dossier où vous souhaitez créer votre projet (pas à la racine htdocs comme demandé)
Exécutez la commande suivante :
bash
composer create-project laravel/laravel le-dressing-des-piplettes
Installation de Vue.js 3 et configuration initiale
Excellent ! Maintenant, nous allons installer Vue.js 3 et ses dépendances dans votre projet Laravel. Suivez ces étapes :
D'abord, naviguez dans le dossier de votre projet :
bash
cd le-dressing-des-piplettes
Installez Vue.js 3 et ses dépendances avec la commande que vous avez spécifiée :
bash
npm install vue@3 vue-loader@17 vue-router@4 vuex@4
Installez également les autres dépendances JavaScript nécessaires pour Laravel :
bash
npm install
Créons maintenant la structure de base pour Vue.js dans votre projet. Commençons par créer les dossiers nécessaires :
bash
mkdir -p resources/js/components
mkdir -p resources/js/views
mkdir -p resources/js/store
resources/js/app.js :
javascript
import './bootstrap';
import { createApp } from 'vue';
import { createStore } from 'vuex';
import { createRouter, createWebHistory } from 'vue-router';
import App from './components/App.vue';

// Import routes
import routes from './routes';

// Create router instance
const router = createRouter({
    history: createWebHistory(),
    routes
});

// Create Vuex store
const store = createStore({
    state() {
        return {
            // Your initial state here
        }
    },
    mutations: {
        // Your mutations here
    },
    actions: {
        // Your actions here
    },
    getters: {
        // Your getters here
    }
});

// Create Vue application
const app = createApp(App);

// Use router and store
app.use(router);
app.use(store);

// Mount the app
app.mount('#app');
mkdir -p resources/js/components

cat > resources/js/components/App.vue << 'EOL'
<template>
    <div>
        <header>
            <h1>Le dressing des piplettes</h1>
            <!-- Navigation -->
        </header>
        
        <main>
            <!-- Router view to display the current page -->
            <router-view></router-view>
        </main>
        
        <footer>
            <!-- Footer content -->
            <p>&copy; {{ new Date().getFullYear() }} Le dressing des piplettes</p>
        </footer>
    </div>
</template>

<script>
export default {
    name: 'App'
}
</script>

<style scoped>
/* Your CSS styles here */
</style>
EOL

cat > resources/js/routes.js << 'EOL'
// Import your view components here
// Example: import Home from './views/Home.vue';

const routes = [
    // Define your routes here
    // Example: { path: '/', component: Home, name: 'home' },
];

export default routes;
EOL

Créons également le dossier pour les vues :
bash
mkdir -p resources/js/views
Et le dossier pour le store Vuex :
bash
mkdir -p resources/js/store
npm install @vitejs/plugin-vue --save-dev

cat > vite.config.js << 'EOL'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                }
            }
        }),
    ],
});
EOL

cat > resources/views/app.blade.php << 'EOL'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Le dressing des piplettes</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div id="app"></div>
    </body>
</html>
EOL


Maintenant, nous devons créer un contrôleur pour notre API backend. Commençons par créer un contrôleur pour gérer les produits :
bash
php artisan make:controller API/ProductController --api
Cette commande va générer un contrôleur avec les méthodes RESTful de base.
Ensuite, créons un contrôleur pour gérer les commandes :
bash
php artisan make:controller API/OrderController --api
Commençons par créer le modèle et la migration pour les produits :
bash
php artisan make:model Product -m
L'option -m génère automatiquement la migration associée au modèle.
Faisons de même pour les autres entités dont nous aurons besoin :
bash
php artisan make:model Category -m
php artisan make:model Size -m
php artisan make:model Order -m
php artisan make:model OrderItem -m

cat > database/migrations/*_create_products_table.php << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('image_url')->nullable();
            $table->string('reference')->unique();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_live_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
EOL
cat > database/migrations/*_create_categories_table.php << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
EOL
cat > database/migrations/*_create_sizes_table.php << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // S, M, L, XL, etc.
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Table pivot pour gérer le stock par produit et par taille
        Schema::create('product_size', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->constrained()->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_size');
        Schema::dropIfExists('sizes');
    }
};
EOL
cat > database/migrations/*_create_orders_table.php << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, paid, shipped, delivered, cancelled
            $table->boolean('is_live_order')->default(false);
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 8, 2)->default(0);
            $table->timestamp('payment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
EOL
cat > database/migrations/*_create_order_items_table.php << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
EOL
cat > app/Models/Product.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'reference',
        'category_id',
        'is_live_available',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class)
            ->withPivot('stock')
            ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
EOL
cat > app/Models/Category.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
EOL
cat > app/Models/Size.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('stock')
            ->withTimestamps();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
EOL
cat > app/Models/Order.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'is_live_order',
        'notes',
        'total_amount',
        'payment_date',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'is_live_order' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
EOL
cat > app/Models/OrderItem.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'size_id',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
EOL
cat > routes/api.php << 'EOL'
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes pour les produits
Route::apiResource('products', ProductController::class);

// Routes pour les commandes
Route::apiResource('orders', OrderController::class);

// Route spécifique pour créer une commande depuis le live
Route::post('orders/live', [OrderController::class, 'createLiveOrder']);

// Route pour obtenir les produits disponibles en live
Route::get('products/live', [ProductController::class, 'getLiveProducts']);

// Route pour mettre à jour le stock d'un produit
Route::patch('products/{product}/stock', [ProductController::class, 'updateStock']);
EOL
cat > app/Http/Controllers/API/ProductController.php << 'EOL'
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
     * Afficher une liste des produits.
     */
    public function index()
    {
        $products = Product::with(['category', 'sizes'])->get();
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
EOL
cat > app/Http/Controllers/API/OrderController.php << 'EOL'
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
     * Afficher une liste des commandes.
     */
    public function index()
    {
        $orders = Order::with(['items.product', 'items.size'])->get();
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

            // Créer la commande
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'status' => $request->status ?? 'pending',
                'is_live_order' => $request->is_live_order ?? false,
                'notes' => $request->notes,
                'total_amount' => $totalAmount,
            ]);

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
    public function show(Order $order)
    {
        return response()->json($order->load(['items.product', 'items.size']));
    }

    /**
     * Mettre à jour la commande spécifiée.
     */
    public function update(Request $request, Order $order)
    {
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
    public function destroy(Order $order)
    {
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
EOL
touch database/database.sqlitephp artisan migratephp artisan make:seeder CategorySeeder
php artisan make:seeder SizeSeeder
cat > database/seeders/CategorySeeder.php << 'EOL'
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
EOL
cat > database/seeders/SizeSeeder.php << 'EOL'
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
EOL
php artisan make:seeder ProductSeeder

cat > database/seeders/ProductSeeder.php << 'EOL'
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
EOL
cat > database/seeders/DatabaseSeeder.php << 'EOL'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SizeSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
EOL

php artisan db:seed

mkdir -p resources/js/views
cat > resources/js/views/Home.vue << 'EOL'
<template>
  <div class="home">
    <header class="bg-gradient-to-r from-pink-500 to-purple-500 text-white py-16 px-4">
      <div class="container mx-auto text-center">
        <h1 class="text-4xl font-bold mb-4">Le dressing des piplettes</h1>
        <p class="text-xl mb-8">Découvrez nos micro-collections exclusives</p>
        <router-link to="/products" class="bg-white text-purple-700 font-bold py-3 px-6 rounded-full shadow-lg hover:shadow-xl transition-all">
          Explorer les collections
        </router-link>
      </div>
    </header>

    <section class="py-12 px-4">
      <div class="container mx-auto">
        <h2 class="text-3xl font-semibold text-center mb-8">Nos dernières collections</h2>
        <div v-if="loading" class="text-center py-12">
          <p>Chargement des produits...</p>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="product in featuredProducts" :key="product.id" class="bg-white rounded-lg shadow-md overflow-hidden">
            <img :src="product.image_url" :alt="product.name" class="w-full h-64 object-cover">
            <div class="p-4">
              <h3 class="text-xl font-semibold mb-2">{{ product.name }}</h3>
              <p class="text-gray-600 mb-4">{{ product.description }}</p>
              <div class="flex justify-between items-center">
                <span class="text-purple-700 font-bold">{{ product.price }} €</span>
                <router-link :to="`/products/${product.id}`" class="bg-purple-100 text-purple-700 px-4 py-2 rounded hover:bg-purple-200 transition-colors">
                  Voir
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-gray-100 py-12 px-4">
      <div class="container mx-auto text-center">
        <h2 class="text-3xl font-semibold mb-8">Comment ça marche ?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="bg-white p-6 rounded-lg shadow">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-purple-700 text-2xl font-bold">1</span>
            </div>
            <h3 class="text-xl font-semibold mb-2">Découvrez</h3>
            <p class="text-gray-600">Parcourez nos micro-collections exclusives en ligne ou lors de nos lives Facebook.</p>
          </div>
          <div class="bg-white p-6 rounded-lg shadow">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-purple-700 text-2xl font-bold">2</span>
            </div>
            <h3 class="text-xl font-semibold mb-2">Commandez</h3>
            <p class="text-gray-600">Sélectionnez vos articles préférés et ajoutez-les à votre panier.</p>
          </div>
          <div class="bg-white p-6 rounded-lg shadow">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-purple-700 text-2xl font-bold">3</span>
            </div>
            <h3 class="text-xl font-semibold mb-2">Recevez</h3>
            <p class="text-gray-600">Nous préparons votre commande avec soin et vous la livrons rapidement.</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Home',
  data() {
    return {
      loading: true,
      featuredProducts: []
    }
  },
  created() {
    this.fetchProducts();
  },
  methods: {
    fetchProducts() {
      axios.get('/api/products/live')
        .then(response => {
          this.featuredProducts = response.data.slice(0, 6); // Limiter à 6 produits
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des produits:', error);
          this.loading = false;
        });
    }
  }
}
</script>
EOL

cat > resources/js/views/ProductList.vue << 'EOL'
<template>
  <div class="product-list py-12 px-4">
    <div class="container mx-auto">
      <h1 class="text-3xl font-semibold mb-8 text-center">Nos collections</h1>
      
      <div class="flex flex-col md:flex-row mb-8">
        <div class="w-full md:w-1/4 mb-4 md:mb-0 md:pr-4">
          <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-xl font-semibold mb-4">Filtres</h2>
            
            <div class="mb-4">
              <h3 class="font-medium mb-2">Catégories</h3>
              <div v-for="category in categories" :key="category.id" class="mb-2">
                <label class="flex items-center cursor-pointer">
                  <input 
                    type="checkbox" 
                    :value="category.id" 
                    v-model="selectedCategories"
                    class="mr-2"
                  >
                  {{ category.name }}
                </label>
              </div>
            </div>
            
            <div class="mb-4">
              <h3 class="font-medium mb-2">Tailles</h3>
              <div class="flex flex-wrap gap-2">
                <button 
                  v-for="size in sizes" 
                  :key="size.id"
                  @click="toggleSize(size.id)"
                  :class="[
                    'border rounded px-3 py-1', 
                    selectedSizes.includes(size.id) 
                      ? 'bg-purple-500 text-white' 
                      : 'border-gray-300 hover:border-purple-500'
                  ]"
                >
                  {{ size.name }}
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="w-full md:w-3/4">
          <div v-if="loading" class="text-center py-12">
            <p>Chargement des produits...</p>
          </div>
          <div v-else>
            <div v-if="filteredProducts.length === 0" class="text-center py-12">
              <p>Aucun produit ne correspond à vos critères de recherche.</p>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="product in filteredProducts" :key="product.id" class="bg-white rounded-lg shadow-md overflow-hidden">
                <img :src="product.image_url" :alt="product.name" class="w-full h-64 object-cover">
                <div class="p-4">
                  <h3 class="text-xl font-semibold mb-2">{{ product.name }}</h3>
                  <p class="text-gray-600 mb-4 line-clamp-2">{{ product.description }}</p>
                  <div class="flex justify-between items-center">
                    <span class="text-purple-700 font-bold">{{ product.price }} €</span>
                    <router-link :to="`/products/${product.id}`" class="bg-purple-100 text-purple-700 px-4 py-2 rounded hover:bg-purple-200 transition-colors">
                      Voir
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ProductList',
  data() {
    return {
      loading: true,
      products: [],
      categories: [],
      sizes: [],
      selectedCategories: [],
      selectedSizes: []
    };
  },
  computed: {
    filteredProducts() {
      let result = this.products;
      
      // Filtre par catégorie
      if (this.selectedCategories.length > 0) {
        result = result.filter(product => 
          this.selectedCategories.includes(product.category_id)
        );
      }
      
      // Filtre par taille
      if (this.selectedSizes.length > 0) {
        result = result.filter(product => 
          product.sizes.some(size => this.selectedSizes.includes(size.id))
        );
      }
      
      return result;
    }
  },
  created() {
    this.fetchProducts();
    this.fetchCategories();
    this.fetchSizes();
  },
  methods: {
    fetchProducts() {
      axios.get('/api/products')
        .then(response => {
          this.products = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des produits:', error);
          this.loading = false;
        });
    },
    fetchCategories() {
      // Cette requête serait normalement vers une API pour récupérer les catégories
      // Pour simplifier, nous utilisons une liste statique
      this.categories = [
        { id: 1, name: 'Robes' },
        { id: 2, name: 'Hauts' },
        { id: 3, name: 'Bas' },
        { id: 4, name: 'Accessoires' },
        { id: 5, name: 'Vestes & Manteaux' }
      ];
    },
    fetchSizes() {
      // Cette requête serait normalement vers une API pour récupérer les tailles
      // Pour simplifier, nous utilisons une liste statique
      this.sizes = [
        { id: 1, name: 'XS' },
        { id: 2, name: 'S' },
        { id: 3, name: 'M' },
        { id: 4, name: 'L' },
        { id: 5, name: 'XL' },
        { id: 6, name: 'XXL' }
      ];
    },
    toggleSize(sizeId) {
      const index = this.selectedSizes.indexOf(sizeId);
      if (index === -1) {
        this.selectedSizes.push(sizeId);
      } else {
        this.selectedSizes.splice(index, 1);
      }
    }
  }
}
</script>
EOL

cat > resources/js/views/ProductDetail.vue << 'EOL'
<template>
  <div class="product-detail py-12 px-4">
    <div class="container mx-auto">
      <div v-if="loading" class="text-center py-12">
        <p>Chargement du produit...</p>
      </div>
      <div v-else-if="product" class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex flex-col md:flex-row">
          <div class="w-full md:w-1/2">
            <img :src="product.image_url" :alt="product.name" class="w-full h-auto object-cover">
          </div>
          <div class="w-full md:w-1/2 p-6">
            <h1 class="text-3xl font-semibold mb-2">{{ product.name }}</h1>
            <p class="text-gray-500 mb-4">Réf: {{ product.reference }}</p>
            <p class="text-gray-700 mb-6">{{ product.description }}</p>
            
            <div class="mb-6">
              <p class="text-2xl font-bold text-purple-700">{{ product.price }} €</p>
            </div>
            
            <div class="mb-6">
              <h3 class="text-lg font-medium mb-2">Choisir une taille</h3>
              <div class="flex flex-wrap gap-2">
                <button 
                  v-for="size in product.sizes" 
                  :key="size.id"
                  @click="selectedSize = size"
                  :disabled="size.pivot.stock === 0"
                  :class="[
                    'border rounded-md px-4 py-2',
                    selectedSize && selectedSize.id === size.id
                      ? 'bg-purple-500 text-white'
                      : size.pivot.stock > 0
                        ? 'border-gray-300 hover:border-purple-500'
                        : 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed'
                  ]"
                >
                  {{ size.name }}
                  <span v-if="size.pivot.stock === 0">(Épuisé)</span>
                </button>
              </div>
              <p v-if="selectedSize" class="mt-2 text-sm text-gray-600">
                Stock disponible: {{ selectedSize.pivot.stock }}
              </p>
            </div>
            
            <div class="mb-6">
              <h3 class="text-lg font-medium mb-2">Quantité</h3>
              <div class="flex items-center">
                <button 
                  @click="quantity > 1 && quantity--"
                  class="border border-gray-300 rounded-l-md px-3 py-1 hover:bg-gray-100"
                  :disabled="quantity <= 1"
                >
                  -
                </button>
                <input 
                  type="number" 
                  v-model.number="quantity"
                  min="1"
                  :max="selectedSize ? selectedSize.pivot.stock : 1"
                  class="border-t border-b border-gray-300 px-3 py-1 w-16 text-center"
                >
                <button 
                  @click="selectedSize && quantity < selectedSize.pivot.stock && quantity++"
                  class="border border-gray-300 rounded-r-md px-3 py-1 hover:bg-gray-100"
                  :disabled="!selectedSize || quantity >= selectedSize.pivot.stock"
                >
                  +
                </button>
              </div>
            </div>
            
            <button 
              @click="addToCart"
              class="w-full bg-purple-600 text-white font-semibold py-3 px-6 rounded-md hover:bg-purple-700 transition-colors"
              :disabled="!selectedSize || selectedSize.pivot.stock === 0"
              :class="{'opacity-50 cursor-not-allowed': !selectedSize || selectedSize.pivot.stock === 0}"
            >
              Ajouter au panier
            </button>
          </div>
        </div>
      </div>
      <div v-else class="text-center py-12">
        <p>Produit non trouvé.</p>
        <router-link to="/products" class="text-purple-600 hover:underline mt-4 inline-block">
          Retour aux produits
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ProductDetail',
  data() {
    return {
      loading: true,
      product: null,
      selectedSize: null,
      quantity: 1
    };
  },
  created() {
    this.fetchProduct();
  },
  methods: {
    fetchProduct() {
      const productId = this.$route.params.id;
      axios.get(`/api/products/${productId}`)
        .then(response => {
          this.product = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement du produit:', error);
          this.loading = false;
        });
    },
    addToCart() {
      if (!this.selectedSize || this.selectedSize.pivot.stock === 0) {
        return;
      }
      
      // Dans une application réelle, vous ajouteriez ici le produit au panier
      // Par exemple, en utilisant Vuex ou un service dédié
      console.log('Ajout au panier:', {
        product: this.product,
        size: this.selectedSize,
        quantity: this.quantity
      });
      
      // Exemple de message de confirmation
      alert(`${this.quantity} ${this.product.name} (Taille: ${this.selectedSize.name}) ajouté(s) au panier`);
    }
  }
}
</script>
EOL

cat > resources/js/routes.js << 'EOL'
import Home from './views/Home.vue';
import ProductList from './views/ProductList.vue';
import ProductDetail from './views/ProductDetail.vue';

const routes = [
  { 
    path: '/', 
    component: Home, 
    name: 'home' 
  },
  { 
    path: '/products', 
    component: ProductList, 
    name: 'products' 
  },
  { 
    path: '/products/:id', 
    component: ProductDetail, 
    name: 'product-detail' 
  }
];

export default routes;
EOL

mkdir -p resources/js/components
cat > resources/js/components/AppHeader.vue << 'EOL'
<template>
  <header class="bg-white shadow">
    <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
      <router-link to="/" class="text-xl font-bold text-purple-600">
        Le dressing des piplettes
      </router-link>
      
      <div class="hidden md:flex space-x-8">
        <router-link to="/" class="text-gray-700 hover:text-purple-600 transition-colors">
          Accueil
        </router-link>
        <router-link to="/products" class="text-gray-700 hover:text-purple-600 transition-colors">
          Collections
        </router-link>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          À propos
        </a>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          Contact
        </a>
      </div>
      
      <div class="flex items-center space-x-4">
        <button class="text-gray-700 hover:text-purple-600 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </button>
        <button class="md:hidden text-gray-700 hover:text-purple-600 transition-colors" @click="mobileMenuOpen = !mobileMenuOpen">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </nav>
    
    <!-- Mobile menu -->
    <div v-if="mobileMenuOpen" class="md:hidden py-2 px-4 bg-gray-50">
      <router-link to="/" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Accueil
      </router-link>
      <router-link to="/products" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Collections
      </router-link>
      <a href="#" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        À propos
      </a>
      <a href="#" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Contact
      </a>
    </div>
  </header>
</template>

<script>
export default {
  name: 'AppHeader',
  data() {
    return {
      mobileMenuOpen: false
    };
  }
}
</script>
EOL

cat > resources/js/components/App.vue << 'EOL'
<template>
  <div class="app min-h-screen bg-gray-50">
    <AppHeader />
    <main>
      <router-view></router-view>
    </main>
    <footer class="bg-gray-800 text-white py-8 px-4">
      <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 class="text-xl font-semibold mb-4">Le dressing des piplettes</h3>
            <p class="mb-4">Votre boutique de vêtements en ligne avec des micro-collections uniques.</p>
            <div class="flex space-x-4">
              <a href="#" class="text-white hover:text-purple-300 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2.04C6.5 2.04 2 6.53 2 12.06C2 17.06 5.66 21.21 10.44 21.96V14.96H7.9V12.06H10.44V9.85C10.44 7.34 11.93 5.96 14.22 5.96C15.31 5.96 16.45 6.15 16.45 6.15V8.62H15.19C13.95 8.62 13.56 9.39 13.56 10.18V12.06H16.34L15.89 14.96H13.56V21.96C18.34 21.21 22 17.06 22 12.06C22 6.53 17.5 2.04 12 2.04Z" />
                </svg>
              </a>
              <a href="#" class="text-white hover:text-purple-300 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 9.52C10.61 9.52 9.52 10.61 9.52 12C9.52 13.39 10.61 14.48 12 14.48C13.39 14.48 14.48 13.39 14.48 12C14.48 10.61 13.39 9.52 12 9.52ZM12 2H12C10.27 2 8.57 2.01 6.86 2.05C5.52 2.08 4.31 2.32 3.24 3.39C1.44 5.19 1.96 7.71 1.96 12C1.96 14.44 1.76 17.67 3.23 19.78C5.13 22.45 8.28 22 12.02 22C16.07 22 17.87 22.01 19.39 20.49C21.14 18.74 20.96 16.31 20.96 12C20.96 10.04 21.05 8.1 20.57 6.17C20.16 4.56 18.94 3.33 17.32 2.92C16.39 2.72 15.41 2.62 14.42 2.58C13.04 2.52 12.52 2 12 2ZM12 7.58C14.33 7.58 16.23 9.48 16.23 11.81C16.23 14.14 14.33 16.04 12 16.04C9.67 16.04 7.77 14.14 7.77 11.81C7.77 9.48 9.68 7.58 12 7.58ZM17.89 5.5C17.89 6.23 17.3 6.82 16.57 6.82C15.84 6.82 15.25 6.23 15.25 5.5C15.25 4.77 15.84 4.18 16.57 4.18C17.3 4.18 17.89 4.77 17.89 5.5Z" />
                </svg>
              </a>
            </div>
          </div>
          
          <div>
            <h3 class="text-xl font-semibold mb-4">Liens rapides</h3>
            <ul class="space-y-2">
              <li><router-link to="/" class="hover:text-purple-300 transition-colors">Accueil</router-link></li>
              <li><router-link to="/products" class="hover:text-purple-300 transition-colors">Collections</router-link></li>
              <li><a href="#" class="hover:text-purple-300 transition-colors">À propos</a></li>
              <li><a href="#" class="hover:text-purple-300 transition-colors">Contact</a></li>
            </ul>
          </div>
          
          <div>
            <h3 class="text-xl font-semibold mb-4">Contact</h3>
            <address class="not-italic">
              <p class="mb-2">123 Rue de la Mode</p>
              <p class="mb-2">75000 Paris, France</p>
              <p class="mb-2">Téléphone: 01 23 45 67 89</p>
              <p class="mb-2">Email: contact@dressing-piplettes.fr</p>
            </address>
          </div>
        </div>
        
        <div class="border-t border-gray-700 mt-8 pt-8 text-center">
          <p>&copy; {{ new Date().getFullYear() }} Le dressing des piplettes. Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import AppHeader from './AppHeader.vue';

export default {
  name: 'App',
  components: {
    AppHeader
  }
}
</script>
EOL

composer require laravel/sanctum laravel/socialite

php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

php artisan migrate

php artisan make:migration add_social_auth_fields_to_users_table --table=users

cat > database/migrations/*_add_social_auth_fields_to_users_table.php << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('social_id')->nullable()->after('email');
            $table->string('social_type')->nullable()->after('social_id');
            $table->string('avatar')->nullable()->after('social_type');
            $table->boolean('email_verified')->default(false)->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['social_id', 'social_type', 'avatar', 'email_verified']);
        });
    }
};
EOL


php artisan migrate

cat > app/Models/User.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'social_id',
        'social_type',
        'avatar',
        'email_verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'social_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'email_verified' => 'boolean',
    ];

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
EOL

php artisan make:controller API/AuthController


cat > app/Http/Controllers/API/AuthController.php << 'EOL'
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(Request $request)
    {
        // Valider les données d'entrée
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Envoyer un email de vérification (cette partie nécessiterait une implémentation supplémentaire)
        // $this->sendVerificationEmail($user);

        // Générer un token pour l'utilisateur
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Inscription réussie. Veuillez vérifier votre email pour activer votre compte.'
        ], 201);
    }

    /**
     * Connexion d'un utilisateur
     */
    public function login(Request $request)
    {
        // Valider les données d'entrée
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tentative de connexion
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Les identifiants fournis sont incorrects.'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Supprimer les anciens tokens
        $user->tokens()->delete();
        
        // Créer un nouveau token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Connexion réussie.'
        ]);
    }

    /**
     * Déconnexion d'un utilisateur
     */
    public function logout(Request $request)
    {
        // Supprimer le token actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ]);
    }

    /**
     * Récupérer les informations de l'utilisateur connecté
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    /**
     * Rediriger vers le fournisseur OAuth
     */
    public function redirectToProvider($provider)
    {
        // Vérifier que le provider est supporté
        if (!in_array($provider, ['google', 'facebook', 'apple'])) {
            return response()->json([
                'message' => 'Fournisseur non supporté.'
            ], 400);
        }

        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl()
        ]);
    }

    /**
     * Gérer le callback du fournisseur OAuth
     */
    public function handleProviderCallback($provider, Request $request)
    {
        try {
            // Vérifier que le provider est supporté
            if (!in_array($provider, ['google', 'facebook', 'apple'])) {
                return response()->json([
                    'message' => 'Fournisseur non supporté.'
                ], 400);
            }

            // Récupérer l'utilisateur depuis le fournisseur OAuth
            $socialUser = Socialite::driver($provider)->stateless()->user();
            
            // Rechercher l'utilisateur dans notre base de données
            $user = User::where('email', $socialUser->getEmail())->first();
            
            // Si l'utilisateur n'existe pas, le créer
            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'social_id' => $socialUser->getId(),
                    'social_type' => $provider,
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified' => true,
                ]);
            } 
            // Si l'utilisateur existe mais n'a pas de social_id, mettre à jour les informations sociales
            else if (empty($user->social_id)) {
                $user->update([
                    'social_id' => $socialUser->getId(),
                    'social_type' => $provider,
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified' => true,
                ]);
            }
            
            // Supprimer les anciens tokens
            $user->tokens()->delete();
            
            // Générer un nouveau token
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'Connexion réussie via ' . ucfirst($provider)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la connexion sociale.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour le profil de l'utilisateur
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'required_with:password|current_password',
            'password' => [
                'required_with:current_password',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Mise à jour des champs fournis
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('email') && $user->email !== $request->email) {
            $user->email = $request->email;
            $user->email_verified = false;
            // Renvoyer un email de vérification ici si nécessaire
        }
        
        if ($request->has('password') && $request->has('current_password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return response()->json([
            'user' => $user,
            'message' => 'Profil mis à jour avec succès.'
        ]);
    }
}
EOL

php artisan make:migration add_user_id_to_orders_table --table=orders

cat > database/migrations/*_add_user_id_to_orders_table.php << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
EOL

php artisan migrate

cat > app/Models/Order.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'is_live_order',
        'notes',
        'total_amount',
        'payment_date',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'is_live_order' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
EOL

cat > app/Http/Controllers/API/OrderController.php << 'EOL'
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
        if ($request->user() && $request->user()->id !== $order->user_id) {
            // Si l'utilisateur n'est pas admin et que la commande ne lui appartient pas
            // Note: la vérification du rôle admin doit être implémentée
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
        if ($request->user() && $request->user()->id !== $order->user_id) {
            // Si l'utilisateur n'est pas admin et que la commande ne lui appartient pas
            // Note: la vérification du rôle admin doit être implémentée
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
        if ($request->user() && $request->user()->id !== $order->user_id) {
            // Si l'utilisateur n'est pas admin et que la commande ne lui appartient pas
            // Note: la vérification du rôle admin doit être implémentée
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
EOL

cat > routes/api.php << 'EOL'
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes d'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'updateProfile']);
    
    // Routes pour les commandes de l'utilisateur connecté
    Route::get('/user/orders', [OrderController::class, 'index']);
});

// Routes pour les produits
Route::apiResource('products', ProductController::class);

// Routes pour les commandes
Route::apiResource('orders', OrderController::class);

// Route spécifique pour créer une commande depuis le live
Route::post('orders/live', [OrderController::class, 'createLiveOrder']);

// Route pour obtenir les produits disponibles en live
Route::get('products/live', [ProductController::class, 'getLiveProducts']);

// Route pour mettre à jour le stock d'un produit
Route::patch('products/{product}/stock', [ProductController::class, 'updateStock']);
EOL

cat > config/services.php << 'EOL'
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', 'http://localhost:8000/api/auth/google/callback'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI', 'http://localhost:8000/api/auth/facebook/callback'),
    ],

    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect' => env('APPLE_REDIRECT_URI', 'http://localhost:8000/api/auth/apple/callback'),
    ],

];
EOL

cat >> .env << 'EOL'

# OAuth Configuration
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback

FACEBOOK_CLIENT_ID=your-facebook-client-id
FACEBOOK_CLIENT_SECRET=your-facebook-client-secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/api/auth/facebook/callback

APPLE_CLIENT_ID=your-apple-client-id
APPLE_CLIENT_SECRET=your-apple-client-secret
APPLE_REDIRECT_URI=http://localhost:8000/api/auth/apple/callback
EOL

cat > resources/js/views/auth/Login.vue << 'EOL'
<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Connexion à votre compte
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Ou
        <router-link to="/register" class="font-medium text-purple-600 hover:text-purple-500">
          créez un compte si vous n'en avez pas
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="login" class="space-y-6">
          <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-700">{{ errorMessage }}</p>
              </div>
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse e-mail
            </label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" required v-model="email"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input id="password" name="password" type="password" autocomplete="current-password" required v-model="password"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember-me" name="remember-me" type="checkbox" v-model="rememberMe"
                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Se souvenir de moi
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-purple-600 hover:text-purple-500">
                Mot de passe oublié ?
              </a>
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              :class="{ 'opacity-75 cursor-not-allowed': loading }"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Se connecter
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Ou continuer avec
              </span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-3 gap-3">
            <div>
              <a @click.prevent="socialLogin('facebook')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('google')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 12.25C6 11.57 6.13 10.9 6.37 10.28L2.58 7.5C1.89 8.91 1.5 10.47 1.5 12.12C1.5 13.8 1.93 15.41 2.69 16.85L6.39 13.92C6.14 13.4 6 12.85 6 12.25Z" fill="#FBBC05" />
                  <path d="M12.25 6.25C13.84 6.25 15.29 6.88 16.38 7.92L19.7 4.6C17.64 2.69 15.04 1.5 12.25 1.5C7.87 1.5 4.08 4.24 2.58 8.26L6.38 11.04C7.1 8.32 9.47 6.25 12.25 6.25Z" fill="#EA4335" />
                  <path d="M12.25 18.25C9.47 18.25 7.1 16.18 6.37 13.46L2.58 16.23C4.08 20.26 7.87 23 12.25 23C14.93 23 17.62 21.9 19.69 19.99L16.14 17.26C15.14 17.91 13.76 18.25 12.25 18.25Z" fill="#34A853" />
                  <path d="M22.5 12.25C22.5 11.5 22.39 10.69 22.25 10H12.25V14.25H18C17.75 15.75 16.83 16.9 15.53 17.64L19.08 20.37C21.14 18.35 22.5 15.58 22.5 12.25Z" fill="#4285F4" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('apple')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M12.7 10c0-2.8 2.3-4.1 2.3-4.1s-1.3-1.9-3.4-1.9c-1.5 0-2.5.8-3.3.8-.9 0-1.7-.7-3-.7-2.2 0-4.4 1.7-4.4 5.2 0 2.1.8 4.3 1.7 5.7.8 1.2 1.5 2.1 2.7 2.1 1 0 1.4-.6 2.9-.6s1.8.6 2.9.6c1.2 0 2-1.1 2.7-2.1.6-.9.9-1.7 1.1-2.4-1.3-.5-2.2-2-2.2-3.6zM11 5.1c.7-.8 1.8-1.3 2.7-1.4-2.3.4-3.5 2-3.5 2 .4-.1 1.2-.3 2-.3.4-.1.8-.1.8-.3z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      rememberMe: false,
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    async login() {
      this.loading = true;
      this.errorMessage = '';
      
      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password
        });
        
        // Stocker le token et les informations utilisateur dans le localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        // Configurer axios pour inclure le token dans les requêtes futures
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // Rediriger vers la page d'accueil
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur de connexion:', error);
        this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de la connexion.';
      } finally {
        this.loading = false;
      }
    },
    async socialLogin(provider) {
      try {
        // Récupérer l'URL de redirection
        const response = await axios.get(`/api/auth/${provider}`);
        
        // Ouvrir une fenêtre popup pour le processus d'authentification
        const width = 600;
        const height = 600;
        const left = (window.innerWidth / 2) - (width / 2);
        const top = (window.innerHeight / 2) - (height / 2);
        
        const popup = window.open(
          response.data.url,
          `${provider}Auth`,
          `width=${width},height=${height},left=${left},top=${top}`
        );
        
        // Fonction pour vérifier si la popup est fermée
        const checkPopup = setInterval(() => {
          if (popup.closed) {
            clearInterval(checkPopup);
            // Vérifier si l'authentification a réussi en vérifiant le localStorage
            if (localStorage.getItem('token')) {
              this.$router.push('/');
            }
          }
        }, 500);
        
      } catch (error) {
        console.error(`Erreur lors de la connexion avec ${provider}:`, error);
        this.errorMessage = `Une erreur est survenue lors de la connexion avec ${provider}.`;
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/auth/Register.vue << 'EOL'
<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Créer un compte
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Ou
        <router-link to="/login" class="font-medium text-purple-600 hover:text-purple-500">
          connectez-vous si vous avez déjà un compte
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="register" class="space-y-6">
          <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-700" v-html="errorMessage"></p>
              </div>
            </div>
          </div>

          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
              Nom complet
            </label>
            <div class="mt-1">
              <input id="name" name="name" type="text" autocomplete="name" required v-model="name"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse e-mail
            </label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" required v-model="email"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input id="password" name="password" type="password" autocomplete="new-password" required v-model="password"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
            <p class="mt-1 text-xs text-gray-500">
              Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.
            </p>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
              Confirmer le mot de passe
            </label>
            <div class="mt-1">
              <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required v-model="passwordConfirmation"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="flex items-center">
            <input id="terms" name="terms" type="checkbox" required v-model="acceptTerms"
              class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
            />
            <label for="terms" class="ml-2 block text-sm text-gray-900">
              J'accepte les <a href="#" class="text-purple-600 hover:text-purple-500">conditions d'utilisation</a> et la <a href="#" class="text-purple-600 hover:text-purple-500">politique de confidentialité</a>
            </label>
          </div>

          <div>
            <button type="submit" :disabled="loading || !acceptTerms"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              :class="{ 'opacity-75 cursor-not-allowed': loading || !acceptTerms }"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Créer un compte
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Ou continuer avec
              </span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-3 gap-3">
            <div>
              <a @click.prevent="socialLogin('facebook')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('google')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 12.25C6 11.57 6.13 10.9 6.37 10.28L2.58 7.5C1.89 8.91 1.5 10.47 1.5 12.12C1.5 13.8 1.93 15.41 2.69 16.85L6.39 13.92C6.14 13.4 6 12.85 6 12.25Z" fill="#FBBC05" />
                  <path d="M12.25 6.25C13.84 6.25 15.29 6.88 16.38 7.92L19.7 4.6C17.64 2.69 15.04 1.5 12.25 1.5C7.87 1.5 4.08 4.24 2.58 8.26L6.38 11.04C7.1 8.32 9.47 6.25 12.25 6.25Z" fill="#EA4335" />
                  <path d="M12.25 18.25C9.47 18.25 7.1 16.18 6.37 13.46L2.58 16.23C4.08 20.26 7.87 23 12.25 23C14.93 23 17.62 21.9 19.69 19.99L16.14 17.26C15.14 17.91 13.76 18.25 12.25 18.25Z" fill="#34A853" />
                  <path d="M22.5 12.25C22.5 11.5 22.39 10.69 22.25 10H12.25V14.25H18C17.75 15.75 16.83 16.9 15.53 17.64L19.08 20.37C21.14 18.35 22.5 15.58 22.5 12.25Z" fill="#4285F4" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('apple')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M12.7 10c0-2.8 2.3-4.1 2.3-4.1s-1.3-1.9-3.4-1.9c-1.5 0-2.5.8-3.3.8-.9 0-1.7-.7-3-.7-2.2 0-4.4 1.7-4.4 5.2 0 2.1.8 4.3 1.7 5.7.8 1.2 1.5 2.1 2.7 2.1 1 0 1.4-.6 2.9-.6s1.8.6 2.9.6c1.2 0 2-1.1 2.7-2.1.6-.9.9-1.7 1.1-2.4-1.3-.5-2.2-2-2.2-3.6zM11 5.1c.7-.8 1.8-1.3 2.7-1.4-2.3.4-3.5 2-3.5 2 .4-.1 1.2-.3 2-.3.4-.1.8-.1.8-.3z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      passwordConfirmation: '',
      acceptTerms: false,
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    async register() {
      this.loading = true;
      this.errorMessage = '';
      
      if (this.password !== this.passwordConfirmation) {
        this.errorMessage = 'Les mots de passe ne correspondent pas.';
        this.loading = false;
        return;
      }
      
      try {
        const response = await axios.post('/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirmation
        });
        
        // Stocker le token et les informations utilisateur dans le localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        // Configurer axios pour inclure le token dans les requêtes futures
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // Afficher un message de succès et rediriger vers la page d'accueil
        alert(response.data.message);
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur d\'inscription:', error);
        if (error.response?.data?.errors) {
          // Formatter les erreurs de validation
          const errors = error.response.data.errors;
          let errorMsg = '<ul class="list-disc list-inside">';
          for (const field in errors) {
            errors[field].forEach(error => {
              errorMsg += `<li>${error}</li>`;
            });
          }
          errorMsg += '</ul>';
          this.errorMessage = errorMsg;
        } else {
          this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de l\'inscription.';
        }
      } finally {
        this.loading = false;
      }
    },
    async socialLogin(provider) {
      try {
        // Récupérer l'URL de redirection
        const response = await axios.get(`/api/auth/${provider}`);
        
        // Ouvrir une fenêtre popup pour le processus d'authentification
        const width = 600;
        const height = 600;
        const left = (window.innerWidth / 2) - (width / 2);
        const top = (window.innerHeight / 2) - (height / 2);
        
        const popup = window.open(
          response.data.url,
          `${provider}Auth`,
          `width=${width},height=${height},left=${left},top=${top}`
        );
        
        // Fonction pour vérifier si la popup est fermée
        const checkPopup = setInterval(() => {
          if (popup.closed) {
            clearInterval(checkPopup);
            // Vérifier si l'authentification a réussi en vérifiant le localStorage
            if (localStorage.getItem('token')) {
              this.$router.push('/');
            }
          }
        }, 500);
        
      } catch (error) {
        console.error(`Erreur lors de la connexion avec ${provider}:`, error);
        this.errorMessage = `Une erreur est survenue lors de la connexion avec ${provider}.`;
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/auth/Profile.vue << 'EOL'
<template>
  <div class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <h1 class="text-2xl font-semibold mb-6">Mon profil</h1>

          <div v-if="loading" class="text-center py-4">
            <svg class="animate-spin h-8 w-8 text-purple-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-gray-600">Chargement de votre profil...</p>
          </div>

          <div v-else>
            <div v-if="successMessage" class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-green-700">{{ successMessage }}</p>
                </div>
              </div>
            </div>

            <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-red-700" v-html="errorMessage"></p>
                </div>
              </div>
            </div>

            <form @submit.prevent="updateProfile">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="name" v-model="form.name" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                  </div>

                  <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse e-mail</label>
                    <input type="email" id="email" v-model="form.email" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <p v-if="!user.email_verified" class="mt-1 text-sm text-red-600">Email non vérifié</p>
                  </div>
                </div>

                <div>
                  <div class="mb-6">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                    <input type="password" id="current_password" v-model="form.current_password" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">Requis uniquement si vous souhaitez changer de mot de passe</p>
                  </div>

                  <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                    <input type="password" id="password" v-model="form.password" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                  </div>

                  <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="password_confirmation" v-model="form.password_confirmation" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                  </div>
                </div>
              </div>

              <div class="flex justify-end mt-4">
                <button type="submit" :disabled="updating" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" :class="{ 'opacity-75 cursor-not-allowed': updating }">
                  <svg v-if="updating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Enregistrer les modifications
                </button>
              </div>
            </form>

            <div class="mt-12 pt-8 border-t border-gray-200">
              <h2 class="text-xl font-semibold mb-6">Mes commandes</h2>
              
              <div v-if="loadingOrders" class="text-center py-4">
                <p class="text-gray-600">Chargement de vos commandes...</p>
              </div>
              
              <div v-else-if="orders.length === 0" class="text-center py-4">
                <p class="text-gray-600">Vous n'avez pas encore passé de commande.</p>
                <router-link to="/products" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                  Découvrir nos produits
                </router-link>
              </div>
              
              <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Commande</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="order in orders" :key="order.id">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ order.id }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(order.created_at) }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.total_amount }} €</td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getStatusClass(order.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ getStatusLabel(order.status) }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button @click="viewOrderDetails(order)" class="text-purple-600 hover:text-purple-900">Voir détails</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal pour les détails de commande -->
    <div v-if="selectedOrder" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="selectedOrder = null"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Commande #{{ selectedOrder.id }}
                </h3>
                <div class="mt-4">
                  <div class="mb-4">
                    <p class="text-sm text-gray-500">Date: {{ formatDate(selectedOrder.created_at) }}</p>
                    <p class="text-sm text-gray-500">Statut: {{ getStatusLabel(selectedOrder.status) }}</p>
                    <p class="text-sm text-gray-500">Total: {{ selectedOrder.total_amount }} €</p>
                  </div>
                  
                  <h4 class="font-medium text-gray-900 mb-2">Articles commandés</h4>
                  <ul class="divide-y divide-gray-200">
                    <li v-for="item in selectedOrder.items" :key="item.id" class="py-3">
                      <div class="flex items-center">
                        <div v-if="item.product && item.product.image_url" class="flex-shrink-0 h-10 w-10">
                          <img :src="item.product.image_url" :alt="item.product.name" class="h-10 w-10 rounded-md">
                        </div>
                        <div class="ml-3">
                          <p class="text-sm font-medium text-gray-900">{{ item.product ? item.product.name : 'Produit non disponible' }}</p>
                          <p class="text-sm text-gray-500">
                            Taille: {{ item.size ? item.size.name : '?' }} | 
                            Quantité: {{ item.quantity }} | 
                            Prix: {{ item.price }} €
                          </p>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" @click="selectedOrder = null" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Profile',
  data() {
    return {
      loading: true,
      updating: false,
      loadingOrders: true,
      user: {},
      form: {
        name: '',
        email: '',
        current_password: '',
        password: '',
        password_confirmation: ''
      },
      orders: [],
      selectedOrder: null,
      successMessage: '',
      errorMessage: ''
    };
  },
  created() {
    this.fetchUserData();
    this.fetchOrders();
  },
  methods: {
    async fetchUserData() {
      try {
        const response = await axios.get('/api/user');
        this.user = response.data.user;
        this.form.name = this.user.name;
        this.form.email = this.user.email;
      } catch (error) {
        console.error('Erreur lors du chargement des données utilisateur:', error);
        this.errorMessage = 'Impossible de charger vos informations personnelles.';
      } finally {
        this.loading = false;
      }
    },
    async fetchOrders() {
      try {
        const response = await axios.get('/api/user/orders');
        this.orders = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des commandes:', error);
      } finally {
        this.loadingOrders = false;
      }
    },
    async updateProfile() {
      this.updating = true;
      this.successMessage = '';
      this.errorMessage = '';
      
      // Ne pas envoyer les champs de mot de passe s'ils sont vides
      const formData = {};
      if (this.form.name !== this.user.name) {
        formData.name = this.form.name;
      }
      if (this.form.email !== this.user.email) {
        formData.email = this.form.email;
      }
      if (this.form.current_password && this.form.password) {
        formData.current_password = this.form.current_password;
        formData.password = this.form.password;
        formData.password_confirmation = this.form.password_confirmation;
      }
      
      try {
        const response = await axios.put('/api/user', formData);
        this.user = response.data.user;
        
        // Mettre à jour les informations stockées localement
        localStorage.setItem('user', JSON.stringify(this.user));
        
        // Réinitialiser les champs de mot de passe
        this.form.current_password = '';
        this.form.password = '';
        this.form.password_confirmation = '';
        
        this.successMessage = response.data.message;
      } catch (error) {
        console.error('Erreur lors de la mise à jour du profil:', error);
        if (error.response?.data?.errors) {
          // Formatter les erreurs de validation
          const errors = error.response.data.errors;
          let errorMsg = '<ul class="list-disc list-inside">';
          for (const field in errors) {
            errors[field].forEach(error => {
              errorMsg += `<li>${error}</li>`;
            });
          }
          errorMsg += '</ul>';
          this.errorMessage = errorMsg;
        } else {
          this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de la mise à jour du profil.';
        }
      } finally {
        this.updating = false;
      }
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(date);
    },
    getStatusClass(status) {
      const statusClasses = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      };
      return statusClasses[status] || 'bg-gray-100 text-gray-800';
    },
    getStatusLabel(status) {
      const statusLabels = {
        pending: 'En attente',
        confirmed: 'Confirmée',
        paid: 'Payée',
        shipped: 'Expédiée',
        delivered: 'Livrée',
        cancelled: 'Annulée'
      };
      return statusLabels[status] || status;
    },
    viewOrderDetails(order) {
      this.selectedOrder = order;
    }
  }
};
</script>
EOL

cat > resources/js/routes.js << 'EOL'
import Home from './views/Home.vue';
import ProductList from './views/ProductList.vue';
import ProductDetail from './views/ProductDetail.vue';
import Login from './views/auth/Login.vue';
import Register from './views/auth/Register.vue';
import Profile from './views/auth/Profile.vue';

// Fonction pour vérifier si l'utilisateur est authentifié
const requireAuth = (to, from, next) => {
  if (!localStorage.getItem('token')) {
    next('/login');
  } else {
    next();
  }
};

// Fonction pour rediriger les utilisateurs déjà connectés
const redirectIfAuthenticated = (to, from, next) => {
  if (localStorage.getItem('token')) {
    next('/');
  } else {
    next();
  }
};

const routes = [
  { 
    path: '/', 
    component: Home, 
    name: 'home' 
  },
  { 
    path: '/products', 
    component: ProductList, 
    name: 'products' 
  },
  { 
    path: '/products/:id', 
    component: ProductDetail, 
    name: 'product-detail' 
  },
  {
    path: '/login',
    component: Login,
    name: 'login',
    beforeEnter: redirectIfAuthenticated
  },
  {
    path: '/register',
    component: Register,
    name: 'register',
    beforeEnter: redirectIfAuthenticated
  },
  {
    path: '/profile',
    component: Profile,
    name: 'profile',
    beforeEnter: requireAuth
  }
];

export default routes;
EOL

cat > resources/js/components/AppHeader.vue << 'EOL'
<template>
  <header class="bg-white shadow">
    <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
      <router-link to="/" class="text-xl font-bold text-purple-600">
        Le dressing des piplettes
      </router-link>
      
      <div class="hidden md:flex space-x-8">
        <router-link to="/" class="text-gray-700 hover:text-purple-600 transition-colors">
          Accueil
        </router-link>
        <router-link to="/products" class="text-gray-700 hover:text-purple-600 transition-colors">
          Collections
        </router-link>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          À propos
        </a>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          Contact
        </a>
      </div>
      
      <div class="flex items-center space-x-4">
        <!-- Panier -->
        <button class="text-gray-700 hover:text-purple-600 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </button>
        
        <!-- Menu utilisateur (connecté) -->
        <div v-if="isLoggedIn" class="relative">
          <button @click="userMenuOpen = !userMenuOpen" class="flex items-center text-gray-700 hover:text-purple-600 transition-colors focus:outline-none">
            <span class="mr-1">{{ user ? user.name : '' }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
          
          <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
            <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Mon profil
            </router-link>
            <a href="#" @click.prevent="logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Déconnexion
            </a>
          </div>
        </div>
        
        <!-- Boutons Connexion/Inscription (non connecté) -->
        <div v-else class="flex items-center space-x-2">
          <router-link to="/login" class="text-gray-700 hover:text-purple-600 transition-colors">
            Connexion
          </router-link>
          <span class="text-gray-300">|</span>
          <router-link to="/register" class="text-gray-700 hover:text-purple-600 transition-colors">
            Inscription
          </router-link>
        </div>
        
        <!-- Bouton menu mobile -->
        <button class="md:hidden text-gray-700 hover:text-purple-600 transition-colors" @click="mobileMenuOpen = !mobileMenuOpen">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </nav>
    
    <!-- Menu mobile -->
    <div v-if="mobileMenuOpen" class="md:hidden py-2 px-4 bg-gray-50">
      <router-link to="/" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Accueil
      </router-link>
      <router-link to="/products" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Collections
      </router-link>
      <a href="#" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        À propos
      </a>
      <a href="#" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Contact
      </a>
      
      <!-- Options utilisateur mobile -->
      <div v-if="isLoggedIn" class="border-t border-gray-200 mt-2 pt-2">
        <router-link to="/profile" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Mon profil
        </router-link>
        <a href="#" @click.prevent="logout" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Déconnexion
        </a>
      </div>
      <div v-else class="border-t border-gray-200 mt-2 pt-2">
        <router-link to="/login" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Connexion
        </router-link>
        <router-link to="/register" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Inscription
        </router-link>
      </div>
    </div>
  </header>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AppHeader',
  data() {
    return {
      mobileMenuOpen: false,
      userMenuOpen: false,
      isLoggedIn: false,
      user: null
    };
  },
  created() {
    this.checkAuth();
    
    // Écouter l'événement de stockage pour les changements d'authentification
    window.addEventListener('storage', this.handleStorageChange

cat > resources/js/components/AppHeader.vue << 'EOL'
<template>
  <header class="bg-white shadow">
    <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
      <router-link to="/" class="text-xl font-bold text-purple-600">
        Le dressing des piplettes
      </router-link>
      
      <div class="hidden md:flex space-x-8">
        <router-link to="/" class="text-gray-700 hover:text-purple-600 transition-colors">
          Accueil
        </router-link>
        <router-link to="/products" class="text-gray-700 hover:text-purple-600 transition-colors">
          Collections
        </router-link>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          À propos
        </a>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          Contact
        </a>
      </div>
      
      <div class="flex items-center space-x-4">
        <!-- Panier -->
        <button class="text-gray-700 hover:text-purple-600 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </button>
        
        <!-- Menu utilisateur (connecté) -->
        <div v-if="isLoggedIn" class="relative">
          <button @click="userMenuOpen = !userMenuOpen" class="flex items-center text-gray-700 hover:text-purple-600 transition-colors focus:outline-none">
            <span class="mr-1">{{ user ? user.name : '' }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
          
          <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
            <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Mon profil
            </router-link>
            <a href="#" @click.prevent="logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Déconnexion
            </a>
          </div>
        </div>
        
        <!-- Boutons Connexion/Inscription (non connecté) -->
        <div v-else class="flex items-center space-x-2">
          <router-link to="/login" class="text-gray-700 hover:text-purple-600 transition-colors">
            Connexion
          </router-link>
          <span class="text-gray-300">|</span>
          <router-link to="/register" class="text-gray-700 hover:text-purple-600 transition-colors">
            Inscription
          </router-link>
        </div>
        
        <!-- Bouton menu mobile -->
        <button class="md:hidden text-gray-700 hover:text-purple-600 transition-colors" @click="mobileMenuOpen = !mobileMenuOpen">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </nav>
    
    <!-- Menu mobile -->
    <div v-if="mobileMenuOpen" class="md:hidden py-2 px-4 bg-gray-50">
      <router-link to="/" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Accueil
      </router-link>
      <router-link to="/products" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Collections
      </router-link>
      <a href="#" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        À propos
      </a>
      <a href="#" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Contact
      </a>
      
      <!-- Options utilisateur mobile -->
      <div v-if="isLoggedIn" class="border-t border-gray-200 mt-2 pt-2">
        <router-link to="/profile" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Mon profil
        </router-link>
        <a href="#" @click.prevent="logout" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Déconnexion
        </a>
      </div>
      <div v-else class="border-t border-gray-200 mt-2 pt-2">
        <router-link to="/login" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Connexion
        </router-link>
        <router-link to="/register" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Inscription
        </router-link>
      </div>
    </div>
  </header>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AppHeader',
  data() {
    return {
      mobileMenuOpen: false,
      userMenuOpen: false,
      isLoggedIn: false,
      user: null
    };
  },
  created() {
    this.checkAuth();
    
    // Écouter l'événement de stockage pour les changements d'authentification
    window.addEventListener('storage', this.handleStorageChange);
    
    // Écouter un événement personnalisé pour les changements d'authentification
    window.addEventListener('auth-changed', this.checkAuth);
  },
  beforeUnmount() {
    // Nettoyer les écouteurs d'événements
    window.removeEventListener('storage', this.handleStorageChange);
    window.removeEventListener('auth-changed', this.checkAuth);
  },
  methods: {
    checkAuth() {
      const token = localStorage.getItem('token');
      const userJson = localStorage.getItem('user');
      
      this.isLoggedIn = !!token;
      this.user = userJson ? JSON.parse(userJson) : null;
      
      // Configurer axios avec le token
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      } else {
        delete axios.defaults.headers.common['Authorization'];
      }
    },
    handleStorageChange(event) {
      // Réagir aux changements de stockage (utile pour la synchronisation entre onglets)
      if (event.key === 'token' || event.key === 'user') {
        this.checkAuth();
      }
    },
    async logout() {
      try {
        // Fermer les menus
        this.userMenuOpen = false;
        this.mobileMenuOpen = false;
        
        // Appeler l'API de déconnexion
        await axios.post('/api/logout');
        
        // Supprimer les données d'authentification
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        
        // Mettre à jour l'état d'authentification
        this.isLoggedIn = false;
        this.user = null;
        
        // Supprimer le token des en-têtes axios
        delete axios.defaults.headers.common['Authorization'];
        
        // Rediriger vers la page d'accueil
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur lors de la déconnexion:', error);
        
        // En cas d'erreur, déconnecter quand même localement
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        this.isLoggedIn = false;
        this.user = null;
        delete axios.defaults.headers.common['Authorization'];
        this.$router.push('/');
      }
    }
  }
};
</script>
EOL

cat > resources/js/app.js << 'EOL'
import './bootstrap';
import { createApp } from 'vue';
import { createStore } from 'vuex';
import { createRouter, createWebHistory } from 'vue-router';
import App from './components/App.vue';
import axios from 'axios';

// Import routes
import routes from './routes';

// Create router instance
const router = createRouter({
    history: createWebHistory(),
    routes
});

// Create Vuex store
const store = createStore({
    state() {
        return {
            // Initial state
            cart: [],
            user: JSON.parse(localStorage.getItem('user') || 'null'),
            isAuthenticated: !!localStorage.getItem('token')
        }
    },
    mutations: {
        // User mutations
        setUser(state, user) {
            state.user = user;
            state.isAuthenticated = !!user;
            
            if (user) {
                localStorage.setItem('user', JSON.stringify(user));
            } else {
                localStorage.removeItem('user');
            }
        },
        setToken(state, token) {
            if (token) {
                localStorage.setItem('token', token);
            } else {
                localStorage.removeItem('token');
            }
        },
        
        // Cart mutations
        addToCart(state, item) {
            const existingItem = state.cart.find(
                i => i.product.id === item.product.id && i.size.id === item.size.id
            );
            
            if (existingItem) {
                existingItem.quantity += item.quantity;
            } else {
                state.cart.push(item);
            }
        },
        removeFromCart(state, index) {
            state.cart.splice(index, 1);
        },
        updateCartItem(state, { index, quantity }) {
            state.cart[index].quantity = quantity;
        },
        clearCart(state) {
            state.cart = [];
        }
    },
    actions: {
        // Authentication actions
        async login({ commit }, credentials) {
            const response = await axios.post('/api/login', credentials);
            const { user, token } = response.data;
            
            commit('setUser', user);
            commit('setToken', token);
            
            // Configure axios with token
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            
            return user;
        },
        async register({ commit }, userData) {
            const response = await axios.post('/api/register', userData);
            const { user, token } = response.data;
            
            commit('setUser', user);
            commit('setToken', token);
            
            // Configure axios with token
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            
            return user;
        },
        async logout({ commit }) {
            try {
                await axios.post('/api/logout');
            } catch (error) {
                console.error('Erreur lors de la déconnexion:', error);
            }
            
            commit('setUser', null);
            commit('setToken', null);
            
            // Remove axios authorization header
            delete axios.defaults.headers.common['Authorization'];
        },
        
        // Cart actions
        checkout({ state, commit }) {
            // This would typically call an API to create an order
            console.log('Checkout with cart items:', state.cart);
            commit('clearCart');
        }
    },
    getters: {
        isAuthenticated: state => state.isAuthenticated,
        user: state => state.user,
        cart: state => state.cart,
        cartTotal: state => {
            return state.cart.reduce((total, item) => {
                return total + (item.product.price * item.quantity);
            }, 0);
        },
        cartItemCount: state => {
            return state.cart.reduce((count, item) => {
                return count + item.quantity;
            }, 0);
        }
    }
});

// Configure axios
// Set the base URL to your API
axios.defaults.baseURL = '/';

// Add JWT token to headers if it exists
const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Add interceptor for 401 responses (unauthorized)
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            // If we get a 401, clear auth data and redirect to login
            store.commit('setUser', null);
            store.commit('setToken', null);
            router.push('/login');
        }
        return Promise.reject(error);
    }
);

// Create Vue application
const app = createApp(App);

// Use router and store
app.use(router);
app.use(store);

// Mount the app
app.mount('#app');
EOL

php artisan make:middleware CheckUserRole

cat > app/Http/Middleware/CheckUserRole.php << 'EOL'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Pour l'instant, nous n'avons pas implémenté de système de rôles
        // Ceci est juste un exemple pour montrer comment vous pourriez le faire
        if ($role === 'admin' && !$request->user()->is_admin) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        return $next($request);
    }
}
EOL

php artisan make:migration add_is_admin_to_users_table --table=users

cat > database/migrations/*_add_is_admin_to_users_table.php << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
EOL

php artisan migrate

cat > app/Models/User.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'social_id',
        'social_type',
        'avatar',
        'email_verified',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'social_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'email_verified' => 'boolean',
        'is_admin' => 'boolean',
    ];

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
EOL

php artisan make:middleware CheckUserRole

cat > bootstrap/app.php << 'EOL'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
EOL

php artisan make:seeder AdminUserSeeder

cat > database/seeders/AdminUserSeeder.php << 'EOL'
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@dressingdespiplettes.com',
            'password' => Hash::make('Admin123!'),
            'email_verified' => true,
            'is_admin' => true,
        ]);
    }
}
EOL

cat > database/seeders/DatabaseSeeder.php << 'EOL'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SizeSeeder::class,
            ProductSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
EOL

php artisan db:seed --class=AdminUserSeeder

Email : admin@dressingdespiplettes.com
Mot de passe : Admin123!
Droits d'administration : Oui

mkdir -p resources/js/views/auth

cat > resources/js/views/auth/Login.vue << 'EOL'
<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Connexion à votre compte
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Ou
        <router-link to="/register" class="font-medium text-purple-600 hover:text-purple-500">
          créez un compte si vous n'en avez pas
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="login" class="space-y-6">
          <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-700">{{ errorMessage }}</p>
              </div>
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse e-mail
            </label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" required v-model="email"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input id="password" name="password" type="password" autocomplete="current-password" required v-model="password"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember-me" name="remember-me" type="checkbox" v-model="rememberMe"
                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Se souvenir de moi
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-purple-600 hover:text-purple-500">
                Mot de passe oublié ?
              </a>
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              :class="{ 'opacity-75 cursor-not-allowed': loading }"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Se connecter
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Ou continuer avec
              </span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-3 gap-3">
            <div>
              <a @click.prevent="socialLogin('facebook')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('google')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 12.25C6 11.57 6.13 10.9 6.37 10.28L2.58 7.5C1.89 8.91 1.5 10.47 1.5 12.12C1.5 13.8 1.93 15.41 2.69 16.85L6.39 13.92C6.14 13.4 6 12.85 6 12.25Z" fill="#FBBC05" />
                  <path d="M12.25 6.25C13.84 6.25 15.29 6.88 16.38 7.92L19.7 4.6C17.64 2.69 15.04 1.5 12.25 1.5C7.87 1.5 4.08 4.24 2.58 8.26L6.38 11.04C7.1 8.32 9.47 6.25 12.25 6.25Z" fill="#EA4335" />
                  <path d="M12.25 18.25C9.47 18.25 7.1 16.18 6.37 13.46L2.58 16.23C4.08 20.26 7.87 23 12.25 23C14.93 23 17.62 21.9 19.69 19.99L16.14 17.26C15.14 17.91 13.76 18.25 12.25 18.25Z" fill="#34A853" />
                  <path d="M22.5 12.25C22.5 11.5 22.39 10.69 22.25 10H12.25V14.25H18C17.75 15.75 16.83 16.9 15.53 17.64L19.08 20.37C21.14 18.35 22.5 15.58 22.5 12.25Z" fill="#4285F4" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('apple')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M12.7 10c0-2.8 2.3-4.1 2.3-4.1s-1.3-1.9-3.4-1.9c-1.5 0-2.5.8-3.3.8-.9 0-1.7-.7-3-.7-2.2 0-4.4 1.7-4.4 5.2 0 2.1.8 4.3 1.7 5.7.8 1.2 1.5 2.1 2.7 2.1 1 0 1.4-.6 2.9-.6s1.8.6 2.9.6c1.2 0 2-1.1 2.7-2.1.6-.9.9-1.7 1.1-2.4-1.3-.5-2.2-2-2.2-3.6zM11 5.1c.7-.8 1.8-1.3 2.7-1.4-2.3.4-3.5 2-3.5 2 .4-.1 1.2-.3 2-.3.4-.1.8-.1.8-.3z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      rememberMe: false,
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    async login() {
      this.loading = true;
      this.errorMessage = '';
      
      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password
        });
        
        // Stocker le token et les informations utilisateur dans le localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        // Configurer axios pour inclure le token dans les requêtes futures
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // Rediriger vers la page d'accueil
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur de connexion:', error);
        this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de la connexion.';
      } finally {
        this.loading = false;
      }
    },
    async socialLogin(provider) {
      try {
        // Récupérer l'URL de redirection
        const response = await axios.get(`/api/auth/${provider}`);
        
        // Ouvrir une fenêtre popup pour le processus d'authentification
        const width = 600;
        const height = 600;
        const left = (window.innerWidth / 2) - (width / 2);
        const top = (window.innerHeight / 2) - (height / 2);
        
        const popup = window.open(
          response.data.url,
          `${provider}Auth`,
          `width=${width},height=${height},left=${left},top=${top}`
        );
        
        // Fonction pour vérifier si la popup est fermée
        const checkPopup = setInterval(() => {
          if (popup.closed) {
            clearInterval(checkPopup);
            // Vérifier si l'authentification a réussi en vérifiant le localStorage
            if (localStorage.getItem('token')) {
              this.$router.push('/');
            }
          }
        }, 500);
        
      } catch (error) {
        console.error(`Erreur lors de la connexion avec ${provider}:`, error);
        this.errorMessage = `Une erreur est survenue lors de la connexion avec ${provider}.`;
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/auth/Register.vue << 'EOL'
<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Créer un compte
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Ou
        <router-link to="/login" class="font-medium text-purple-600 hover:text-purple-500">
          connectez-vous si vous avez déjà un compte
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="register" class="space-y-6">
          <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-700" v-html="errorMessage"></p>
              </div>
            </div>
          </div>

          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
              Nom complet
            </label>
            <div class="mt-1">
              <input id="name" name="name" type="text" autocomplete="name" required v-model="name"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse e-mail
            </label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" required v-model="email"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input id="password" name="password" type="password" autocomplete="new-password" required v-model="password"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
            <p class="mt-1 text-xs text-gray-500">
              Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.
            </p>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
              Confirmer le mot de passe
            </label>
            <div class="mt-1">
              <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required v-model="passwordConfirmation"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="flex items-center">
            <input id="terms" name="terms" type="checkbox" required v-model="acceptTerms"
              class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
            />
            <label for="terms" class="ml-2 block text-sm text-gray-900">
              J'accepte les <a href="#" class="text-purple-600 hover:text-purple-500">conditions d'utilisation</a> et la <a href="#" class="text-purple-600 hover:text-purple-500">politique de confidentialité</a>
            </label>
          </div>

          <div>
            <button type="submit" :disabled="loading || !acceptTerms"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              :class="{ 'opacity-75 cursor-not-allowed': loading || !acceptTerms }"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Créer un compte
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Ou continuer avec
              </span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-3 gap-3">
            <div>
              <a @click.prevent="socialLogin('facebook')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('google')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 12.25C6 11.57 6.13 10.9 6.37 10.28L2.58 7.5C1.89 8.91 1.5 10.47 1.5 12.12C1.5 13.8 1.93 15.41 2.69 16.85L6.39 13.92C6.14 13.4 6 12.85 6 12.25Z" fill="#FBBC05" />
                  <path d="M12.25 6.25C13.84 6.25 15.29 6.88 16.38 7.92L19.7 4.6C17.64 2.69 15.04 1.5 12.25 1.5C7.87 1.5 4.08 4.24 2.58 8.26L6.38 11.04C7.1 8.32 9.47 6.25 12.25 6.25Z" fill="#EA4335" />
                  <path d="M12.25 18.25C9.47 18.25 7.1 16.18 6.37 13.46L2.58 16.23C4.08 20.26 7.87 23 12.25 23C14.93 23 17.62 21.9 19.69 19.99L16.14 17.26C15.14 17.91 13.76 18.25 12.25 18.25Z" fill="#34A853" />
                  <path d="M22.5 12.25C22.5 11.5 22.39 10.69 22.25 10H12.25V14.25H18C17.75 15.75 16.83 16.9 15.53 17.64L19.08 20.37C21.14 18.35 22.5 15.58 22.5 12.25Z" fill="#4285F4" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('apple')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M12.7 10c0-2.8 2.3-4.1 2.3-4.1s-1.3-1.9-3.4-1.9c-1.5 0-2.5.8-3.3.8-.9 0-1.7-.7-3-.7-2.2 0-4.4 1.7-4.4 5.2 0 2.1.8 4.3 1.7 5.7.8 1.2 1.5 2.1 2.7 2.1 1 0 1.4-.6 2.9-.6s1.8.6 2.9.6c1.2 0 2-1.1 2.7-2.1.6-.9.9-1.7 1.1-2.4-1.3-.5-2.2-2-2.2-3.6zM11 5.1c.7-.8 1.8-1.3 2.7-1.4-2.3.4-3.5 2-3.5 2 .4-.1 1.2-.3 2-.3.4-.1.8-.1.8-.3z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      passwordConfirmation: '',
      acceptTerms: false,
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    async register() {
      this.loading = true;
      this.errorMessage = '';
      
      if (this.password !== this.passwordConfirmation) {
        this.errorMessage = 'Les mots de passe ne correspondent pas.';
        this.loading = false;
        return;
      }
      
      try {
        const response = await axios.post('/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirmation
        });
        
        // Stocker le token et les informations utilisateur dans le localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        // Configurer axios pour inclure le token dans les requêtes futures
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // Afficher un message de succès et rediriger vers la page d'accueil
        alert(response.data.message);
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur d\'inscription:', error);
        if (error.response?.data?.errors) {
          // Formatter les erreurs de validation
          const errors = error.response.data.errors;
          let errorMsg = '<ul class="list-disc list-inside">';
          for (const field in errors) {
            errors[field].forEach(error => {
              errorMsg += `<li>${error}</li>`;
            });
          }
          errorMsg += '</ul>';
          this.errorMessage = errorMsg;
        } else {
          this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de l\'inscription.';
        }
      } finally {
        this.loading = false;
      }
    },
    async socialLogin(provider) {
      try {
        // Récupérer l'URL de redirection
        const response = await axios.get(`/api/auth/${provider}`);
        
        // Ouvrir une fenêtre popup pour le processus d'authentification
        const width = 600;
        const height = 600;
        const left = (window.innerWidth / 2) - (width / 2);
        const top = (window.innerHeight / 2) - (height / 2);
        
        const popup = window.open(
          response.data.url,
          `${provider}Auth`,
          `width=${width},height=${height},left=${left},top=${top}`
        );
        
        // Fonction pour vérifier si la popup est fermée
        const checkPopup = setInterval(() => {
          if (popup.closed) {
            clearInterval(checkPopup);
            // Vérifier si l'authentification a réussi en vérifiant le localStorage
            if (localStorage.getItem('token')) {
              this.$router.push('/');
            }
          }
        }, 500);
        
      } catch (error) {
        console.error(`Erreur lors de la connexion avec ${provider}:`, error);
        this.errorMessage = `Une erreur est survenue lors de la connexion avec ${provider}.`;
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/auth/Register.vue << 'EOL'
<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Créer un compte
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Ou
        <router-link to="/login" class="font-medium text-purple-600 hover:text-purple-500">
          connectez-vous si vous avez déjà un compte
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="register" class="space-y-6">
          <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-700" v-html="errorMessage"></p>
              </div>
            </div>
          </div>

          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
              Nom complet
            </label>
            <div class="mt-1">
              <input id="name" name="name" type="text" autocomplete="name" required v-model="name"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse e-mail
            </label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" required v-model="email"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input id="password" name="password" type="password" autocomplete="new-password" required v-model="password"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
            <p class="mt-1 text-xs text-gray-500">
              Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.
            </p>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
              Confirmer le mot de passe
            </label>
            <div class="mt-1">
              <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required v-model="passwordConfirmation"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="flex items-center">
            <input id="terms" name="terms" type="checkbox" required v-model="acceptTerms"
              class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
            />
            <label for="terms" class="ml-2 block text-sm text-gray-900">
              J'accepte les <a href="#" class="text-purple-600 hover:text-purple-500">conditions d'utilisation</a> et la <a href="#" class="text-purple-600 hover:text-purple-500">politique de confidentialité</a>
            </label>
          </div>

          <div>
            <button type="submit" :disabled="loading || !acceptTerms"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              :class="{ 'opacity-75 cursor-not-allowed': loading || !acceptTerms }"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Créer un compte
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Ou continuer avec
              </span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-3 gap-3">
            <div>
              <a @click.prevent="socialLogin('facebook')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('google')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 12.25C6 11.57 6.13 10.9 6.37 10.28L2.58 7.5C1.89 8.91 1.5 10.47 1.5 12.12C1.5 13.8 1.93 15.41 2.69 16.85L6.39 13.92C6.14 13.4 6 12.85 6 12.25Z" fill="#FBBC05" />
                  <path d="M12.25 6.25C13.84 6.25 15.29 6.88 16.38 7.92L19.7 4.6C17.64 2.69 15.04 1.5 12.25 1.5C7.87 1.5 4.08 4.24 2.58 8.26L6.38 11.04C7.1 8.32 9.47 6.25 12.25 6.25Z" fill="#EA4335" />
                  <path d="M12.25 18.25C9.47 18.25 7.1 16.18 6.37 13.46L2.58 16.23C4.08 20.26 7.87 23 12.25 23C14.93 23 17.62 21.9 19.69 19.99L16.14 17.26C15.14 17.91 13.76 18.25 12.25 18.25Z" fill="#34A853" />
                  <path d="M22.5 12.25C22.5 11.5 22.39 10.69 22.25 10H12.25V14.25H18C17.75 15.75 16.83 16.9 15.53 17.64L19.08 20.37C21.14 18.35 22.5 15.58 22.5 12.25Z" fill="#4285F4" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('apple')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M12.7 10c0-2.8 2.3-4.1 2.3-4.1s-1.3-1.9-3.4-1.9c-1.5 0-2.5.8-3.3.8-.9 0-1.7-.7-3-.7-2.2 0-4.4 1.7-4.4 5.2 0 2.1.8 4.3 1.7 5.7.8 1.2 1.5 2.1 2.7 2.1 1 0 1.4-.6 2.9-.6s1.8.6 2.9.6c1.2 0 2-1.1 2.7-2.1.6-.9.9-1.7 1.1-2.4-1.3-.5-2.2-2-2.2-3.6zM11 5.1c.7-.8 1.8-1.3 2.7-1.4-2.3.4-3.5 2-3.5 2 .4-.1 1.2-.3 2-.3.4-.1.8-.1.8-.3z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      passwordConfirmation: '',
      acceptTerms: false,
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    async register() {
      this.loading = true;
      this.errorMessage = '';
      
      if (this.password !== this.passwordConfirmation) {
        this.errorMessage = 'Les mots de passe ne correspondent pas.';
        this.loading = false;
        return;
      }
      
      try {
        const response = await axios.post('/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirmation
        });
        
        // Stocker le token et les informations utilisateur dans le localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        // Configurer axios pour inclure le token dans les requêtes futures
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // Afficher un message de succès et rediriger vers la page d'accueil
        alert(response.data.message);
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur d\'inscription:', error);
        if (error.response?.data?.errors) {
          // Formatter les erreurs de validation
          const errors = error.response.data.errors;
          let errorMsg = '<ul class="list-disc list-inside">';
          for (const field in errors) {
            errors[field].forEach(error => {
              errorMsg += `<li>${error}</li>`;
            });
          }
          errorMsg += '</ul>';
          this.errorMessage = errorMsg;
        } else {
          this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de l\'inscription.';
        }
      } finally {
        this.loading = false;
      }
    },
    async socialLogin(provider) {
      try {
        // Récupérer l'URL de redirection
        const response = await axios.get(`/api/auth/${provider}`);
        
        // Ouvrir une fenêtre popup pour le processus d'authentification
        const width = 600;
        const height = 600;
        const left = (window.innerWidth / 2) - (width / 2);
        const top = (window.innerHeight / 2) - (height / 2);
        
        const popup = window.open(
          response.data.url,
          `${provider}Auth`,
          `width=${width},height=${height},left=${left},top=${top}`
        );
        
        // Fonction pour vérifier si la popup est fermée
        const checkPopup = setInterval(() => {
          if (popup.closed) {
            clearInterval(checkPopup);
            // Vérifier si l'authentification a réussi en vérifiant le localStorage
            if (localStorage.getItem('token')) {
              this.$router.push('/');
            }
          }
        }, 500);
        
      } catch (error) {
        console.error(`Erreur lors de la connexion avec ${provider}:`, error);
        this.errorMessage = `Une erreur est survenue lors de la connexion avec ${provider}.`;
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/auth/Profile.vue << 'EOL'
<template>
  <div class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <h1 class="text-2xl font-semibold mb-6">Mon profil</h1>

          <div v-if="loading" class="text-center py-4">
            <svg class="animate-spin h-8 w-8 text-purple-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-gray-600">Chargement de votre profil...</p>
          </div>

          <div v-else>
            <div v-if="successMessage" class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-green-700">{{ successMessage }}</p>
                </div>
              </div>
            </div>

            <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-red-700" v-html="errorMessage"></p>
                </div>
              </div>
            </div>

            <form @submit.prevent="updateProfile">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" id="name" v-model="form.name" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                  </div>

                  <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse e-mail</label>
                    <input type="email" id="email" v-model="form.email" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <p v-if="!user.email_verified" class="mt-1 text-sm text-red-600">Email non vérifié</p>
                  </div>
                </div>

                <div>
                  <div class="mb-6">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                    <input type="password" id="current_password" v-model="form.current_password" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <p class="mt-1 text-xs text-gray-500">Requis uniquement si vous souhaitez changer de mot de passe</p>
                  </div>

                  <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                    <input type="password" id="password" v-model="form.password" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                  </div>

                  <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="password_confirmation" v-model="form.password_confirmation" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md">
                  </div>
                </div>
              </div>

              <div class="flex justify-end mt-4">
                <button type="submit" :disabled="updating" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" :class="{ 'opacity-75 cursor-not-allowed': updating }">
                  <svg v-if="updating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Enregistrer les modifications
                </button>
              </div>
            </form>

            <div class="mt-12 pt-8 border-t border-gray-200">
              <h2 class="text-xl font-semibold mb-6">Mes commandes</h2>
              
              <div v-if="loadingOrders" class="text-center py-4">
                <p class="text-gray-600">Chargement de vos commandes...</p>
              </div>
              
              <div v-else-if="orders.length === 0" class="text-center py-4">
                <p class="text-gray-600">Vous n'avez pas encore passé de commande.</p>
                <router-link to="/products" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                  Découvrir nos produits
                </router-link>
              </div>
              
              <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Commande</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="order in orders" :key="order.id">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ order.id }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(order.created_at) }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.total_amount }} €</td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getStatusClass(order.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ getStatusLabel(order.status) }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button @click="viewOrderDetails(order)" class="text-purple-600 hover:text-purple-900">Voir détails</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal pour les détails de commande -->
    <div v-if="selectedOrder" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="selectedOrder = null"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Commande #{{ selectedOrder.id }}
                </h3>
                <div class="mt-4">
                  <div class="mb-4">
                    <p class="text-sm text-gray-500">Date: {{ formatDate(selectedOrder.created_at) }}</p>
                    <p class="text-sm text-gray-500">Statut: {{ getStatusLabel(selectedOrder.status) }}</p>
                    <p class="text-sm text-gray-500">Total: {{ selectedOrder.total_amount }} €</p>
                  </div>
                  
                  <h4 class="font-medium text-gray-900 mb-2">Articles commandés</h4>
                  <ul class="divide-y divide-gray-200">
                    <li v-for="item in selectedOrder.items" :key="item.id" class="py-3">
                      <div class="flex items-center">
                        <div v-if="item.product && item.product.image_url" class="flex-shrink-0 h-10 w-10">
                          <img :src="item.product.image_url" :alt="item.product.name" class="h-10 w-10 rounded-md">
                        </div>
                        <div class="ml-3">
                          <p class="text-sm font-medium text-gray-900">{{ item.product ? item.product.name : 'Produit non disponible' }}</p>
                          <p class="text-sm text-gray-500">
                            Taille: {{ item.size ? item.size.name : '?' }} | 
                            Quantité: {{ item.quantity }} | 
                            Prix: {{ item.price }} €
                          </p>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" @click="selectedOrder = null" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Profile',
  data() {
    return {
      loading: true,
      updating: false,
      loadingOrders: true,
      user: {},
      form: {
        name: '',
        email: '',
        current_password: '',
        password: '',
        password_confirmation: ''
      },
      orders: [],
      selectedOrder: null,
      successMessage: '',
      errorMessage: ''
    };
  },
  created() {
    this.fetchUserData();
    this.fetchOrders();
  },
  methods: {
    async fetchUserData() {
      try {
        const response = await axios.get('/api/user');
        this.user = response.data.user;
        this.form.name = this.user.name;
        this.form.email = this.user.email;
      } catch (error) {
        console.error('Erreur lors du chargement des données utilisateur:', error);
        this.errorMessage = 'Impossible de charger vos informations personnelles.';
      } finally {
        this.loading = false;
      }
    },
    async fetchOrders() {
      try {
        const response = await axios.get('/api/user/orders');
        this.orders = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des commandes:', error);
      } finally {
        this.loadingOrders = false;
      }
    },
    async updateProfile() {
      this.updating = true;
      this.successMessage = '';
      this.errorMessage = '';
      
      // Ne pas envoyer les champs de mot de passe s'ils sont vides
      const formData = {};
      if (this.form.name !== this.user.name) {
        formData.name = this.form.name;
      }
      if (this.form.email !== this.user.email) {
        formData.email = this.form.email;
      }
      if (this.form.current_password && this.form.password) {
        formData.current_password = this.form.current_password;
        formData.password = this.form.password;
        formData.password_confirmation = this.form.password_confirmation;
      }
      
      try {
        const response = await axios.put('/api/user', formData);
        this.user = response.data.user;
        
        // Mettre à jour les informations stockées localement
        localStorage.setItem('user', JSON.stringify(this.user));
        
        // Réinitialiser les champs de mot de passe
        this.form.current_password = '';
        this.form.password = '';
        this.form.password_confirmation = '';
        
        this.successMessage = response.data.message;
      } catch (error) {
        console.error('Erreur lors de la mise à jour du profil:', error);
        if (error.response?.data?.errors) {
          // Formatter les erreurs de validation
          const errors = error.response.data.errors;
          let errorMsg = '<ul class="list-disc list-inside">';
          for (const field in errors) {
            errors[field].forEach(error => {
              errorMsg += `<li>${error}</li>`;
            });
          }
          errorMsg += '</ul>';
          this.errorMessage = errorMsg;
        } else {
          this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de la mise à jour du profil.';
        }
      } finally {
        this.updating = false;
      }
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(date);
    },
    getStatusClass(status) {
      const statusClasses = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      };
      return statusClasses[status] || 'bg-gray-100 text-gray-800';
    },
    getStatusLabel(status) {
      const statusLabels = {
        pending: 'En attente',
        confirmed: 'Confirmée',
        paid: 'Payée',
        shipped: 'Expédiée',
        delivered: 'Livrée',
        cancelled: 'Annulée'
      };
      return statusLabels[status] || status;
    },
    viewOrderDetails(order) {
      this.selectedOrder = order;
    }
  }
};
</script>
EOL

npm run dev

php artisan route:clear
php artisan cache:clear
php artisan config:clear

ls -la app/Http/Controllers/API/AuthController.php



cat bootstrap/app.php        

cat > bootstrap/app.php << 'EOL'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // Ajout de la route API
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
EOL

ls -la routes/api.php

cat > routes/web.php << 'EOL'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;

// Routes API avec préfixe explicite
Route::prefix('api')->group(function () {
    // Routes d'authentification
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    
    // Routes protégées par authentification
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/user', [AuthController::class, 'updateProfile']);
        
        // Routes pour les commandes de l'utilisateur connecté
        Route::get('/user/orders', [OrderController::class, 'index']);
    });
    
    // Routes pour les produits
    Route::apiResource('products', ProductController::class);
    
    // Routes pour les commandes
    Route::apiResource('orders', OrderController::class);
    
    // Route spécifique pour créer une commande depuis le live
    Route::post('orders/live', [OrderController::class, 'createLiveOrder']);
    
    // Route pour obtenir les produits disponibles en live
    Route::get('products/live', [ProductController::class, 'getLiveProducts']);
    
    // Route pour mettre à jour le stock d'un produit
    Route::patch('products/{product}/stock', [ProductController::class, 'updateStock']);
});

// Routes Web pour servir l'application Vue.js
Route::get('/', function () {
    return view('app');
});

// Catch-all route for SPA
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
EOL

cat > bootstrap/app.php << 'EOL'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
EOL

ls -la app/Http/Controllers/API/AuthController.php

php artisan optimize:clear
php artisan route:clear
php artisan config:clear

php artisan vendor:publish --tag=laravel-config

mkdir -p app/Http/Middleware
cat > app/Http/Middleware/VerifyCsrfToken.php << 'EOL'
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*', // Exclure toutes les routes API
    ];
}
EOL


cat > bootstrap/app.php << 'EOL'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
        ]);
        
        // Remplacer le middleware de vérification CSRF par défaut par notre version personnalisée
        $middleware->replace(
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\VerifyCsrfToken::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
EOL

php artisan optimize:clear

cat > resources/js/views/auth/Login.vue << 'EOL'
<template>
  <!-- Le reste du code reste inchangé -->
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      rememberMe: false,
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    async login() {
      this.loading = true;
      this.errorMessage = '';
      
      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password
        });
        
        // Stocker le token et les informations utilisateur dans le localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        // Configurer axios pour inclure le token dans les requêtes futures
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // IMPORTANT: Émettre un événement personnalisé pour notifier les autres composants
        window.dispatchEvent(new CustomEvent('auth-changed'));
        
        // Rediriger vers la page d'accueil
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur de connexion:', error);
        this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de la connexion.';
      } finally {
        this.loading = false;
      }
    },
    async socialLogin(provider) {
      try {
        // Récupérer l'URL de redirection
        const response = await axios.get(`/api/auth/${provider}`);
        
        // Ouvrir une fenêtre popup pour le processus d'authentification
        const width = 600;
        const height = 600;
        const left = (window.innerWidth / 2) - (width / 2);
        const top = (window.innerHeight / 2) - (height / 2);
        
        const popup = window.open(
          response.data.url,
          `${provider}Auth`,
          `width=${width},height=${height},left=${left},top=${top}`
        );
        
        // Fonction pour vérifier si la popup est fermée
        const checkPopup = setInterval(() => {
          if (popup.closed) {
            clearInterval(checkPopup);
            // Vérifier si l'authentification a réussi en vérifiant le localStorage
            if (localStorage.getItem('token')) {
              // Émettre l'événement auth-changed
              window.dispatchEvent(new CustomEvent('auth-changed'));
              this.$router.push('/');
            }
          }
        }, 500);
        
      } catch (error) {
        console.error(`Erreur lors de la connexion avec ${provider}:`, error);
        this.errorMessage = `Une erreur est survenue lors de la connexion avec ${provider}.`;
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/auth/Register.vue << 'EOL'
<template>
  <!-- Le reste du code reste inchangé -->
</template>

<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      passwordConfirmation: '',
      acceptTerms: false,
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    async register() {
      this.loading = true;
      this.errorMessage = '';
      
      if (this.password !== this.passwordConfirmation) {
        this.errorMessage = 'Les mots de passe ne correspondent pas.';
        this.loading = false;
        return;
      }
      
      try {
        const response = await axios.post('/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirmation
        });
        
        // Stocker le token et les informations utilisateur dans le localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        // Configurer axios pour inclure le token dans les requêtes futures
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // IMPORTANT: Émettre un événement personnalisé pour notifier les autres composants
        window.dispatchEvent(new CustomEvent('auth-changed'));
        
        // Afficher un message de succès et rediriger vers la page d'accueil
        alert(response.data.message);
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur d\'inscription:', error);
        if (error.response?.data?.errors) {
          // Formatter les erreurs de validation
          const errors = error.response.data.errors;
          let errorMsg = '<ul class="list-disc list-inside">';
          for (const field in errors) {
            errors[field].forEach(error => {
              errorMsg += `<li>${error}</li>`;
            });
          }
          errorMsg += '</ul>';
          this.errorMessage = errorMsg;
        } else {
          this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de l\'inscription.';
        }
      } finally {
        this.loading = false;
      }
    },
    async socialLogin(provider) {
      try {
        // Récupérer l'URL de redirection
        const response = await axios.get(`/api/auth/${provider}`);
        
        // Ouvrir une fenêtre popup pour le processus d'authentification
        const width = 600;
        const height = 600;
        const left = (window.innerWidth / 2) - (width / 2);
        const top = (window.innerHeight / 2) - (height / 2);
        
        const popup = window.open(
          response.data.url,
          `${provider}Auth`,
          `width=${width},height=${height},left=${left},top=${top}`
        );
        
        // Fonction pour vérifier si la popup est fermée
        const checkPopup = setInterval(() => {
          if (popup.closed) {
            clearInterval(checkPopup);
            // Vérifier si l'authentification a réussi en vérifiant le localStorage
            if (localStorage.getItem('token')) {
              // Émettre l'événement auth-changed
              window.dispatchEvent(new CustomEvent('auth-changed'));
              this.$router.push('/');
            }
          }
        }, 500);
        
      } catch (error) {
        console.error(`Erreur lors de la connexion avec ${provider}:`, error);
        this.errorMessage = `Une erreur est survenue lors de la connexion avec ${provider}.`;
      }
    }
  }
};
</script>
EOL
Modifiez la méthode de déconnexion dans AppHeader.vue pour également émettre l'événement :
javascript
async logout() {
  try {
    // Fermer les menus
    this.userMenuOpen = false;
    this.mobileMenuOpen = false;
    
    // Appeler l'API de déconnexion
    await axios.post('/api/logout');
    
    // Supprimer les données d'authentification
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    
    // Mettre à jour l'état d'authentification
    this.isLoggedIn = false;
    this.user = null;
    
    // Supprimer le token des en-têtes axios
    delete axios.defaults.headers.common['Authorization'];
    
    // Émettre l'événement auth-changed
    window.dispatchEvent(new CustomEvent('auth-changed'));
    
    // Rediriger vers la page d'accueil
    this.$router.push('/');
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error);
    
    // En cas d'erreur, déconnecter quand même localement
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    this.isLoggedIn = false;
    this.user = null;
    delete axios.defaults.headers.common['Authorization'];
    window.dispatchEvent(new CustomEvent('auth-changed'));
    this.$router.push('/');
  }
}

cat > resources/js/views/auth/Login.vue << 'EOL'
<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Connexion à votre compte
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Ou
        <router-link to="/register" class="font-medium text-purple-600 hover:text-purple-500">
          créez un compte si vous n'en avez pas
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="login" class="space-y-6">
          <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-700">{{ errorMessage }}</p>
              </div>
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse e-mail
            </label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" required v-model="email"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input id="password" name="password" type="password" autocomplete="current-password" required v-model="password"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember-me" name="remember-me" type="checkbox" v-model="rememberMe"
                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Se souvenir de moi
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-purple-600 hover:text-purple-500">
                Mot de passe oublié ?
              </a>
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              :class="{ 'opacity-75 cursor-not-allowed': loading }"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Se connecter
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Ou continuer avec
              </span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-3 gap-3">
            <div>
              <a @click.prevent="socialLogin('facebook')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('google')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 12.25C6 11.57 6.13 10.9 6.37 10.28L2.58 7.5C1.89 8.91 1.5 10.47 1.5 12.12C1.5 13.8 1.93 15.41 2.69 16.85L6.39 13.92C6.14 13.4 6 12.85 6 12.25Z" fill="#FBBC05" />
                  <path d="M12.25 6.25C13.84 6.25 15.29 6.88 16.38 7.92L19.7 4.6C17.64 2.69 15.04 1.5 12.25 1.5C7.87 1.5 4.08 4.24 2.58 8.26L6.38 11.04C7.1 8.32 9.47 6.25 12.25 6.25Z" fill="#EA4335" />
                  <path d="M12.25 18.25C9.47 18.25 7.1 16.18 6.37 13.46L2.58 16.23C4.08 20.26 7.87 23 12.25 23C14.93 23 17.62 21.9 19.69 19.99L16.14 17.26C15.14 17.91 13.76 18.25 12.25 18.25Z" fill="#34A853" />
                  <path d="M22.5 12.25C22.5 11.5 22.39 10.69 22.25 10H12.25V14.25H18C17.75 15.75 16.83 16.9 15.53 17.64L19.08 20.37C21.14 18.35 22.5 15.58 22.5 12.25Z" fill="#4285F4" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('apple')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M12.7 10c0-2.8 2.3-4.1 2.3-4.1s-1.3-1.9-3.4-1.9c-1.5 0-2.5.8-3.3.8-.9 0-1.7-.7-3-.7-2.2 0-4.4 1.7-4.4 5.2 0 2.1.8 4.3 1.7 5.7.8 1.2 1.5 2.1 2.7 2.1 1 0 1.4-.6 2.9-.6s1.8.6 2.9.6c1.2 0 2-1.1 2.7-2.1.6-.9.9-1.7 1.1-2.4-1.3-.5-2.2-2-2.2-3.6zM11 5.1c.7-.8 1.8-1.3 2.7-1.4-2.3.4-3.5 2-3.5 2 .4-.1 1.2-.3 2-.3.4-.1.8-.1.8-.3z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      rememberMe: false,
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    async login() {
      this.loading = true;
      this.errorMessage = '';
      
      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password
        });
        
        // Stocker le token et les informations utilisateur dans le localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        // Configurer axios pour inclure le token dans les requêtes futures
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // Émettre un événement personnalisé pour notifier les autres composants
        window.dispatchEvent(new CustomEvent('auth-changed'));
        
        // Rediriger vers la page d'accueil
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur de connexion:', error);
        this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de la connexion.';
      } finally {
        this.loading = false;
      }
    },
    async socialLogin(provider) {
      try {
        // Récupérer l'URL de redirection
        const response = await axios.get(`/api/auth/${provider}`);
        
        // Ouvrir une fenêtre popup pour le processus d'authentification
        const width = 600;
        const height = 600;
        const left = (window.innerWidth / 2) - (width / 2);
        const top = (window.innerHeight / 2) - (height / 2);
        
        const popup = window.open(
          response.data.url,
          `${provider}Auth`,
          `width=${width},height=${height},left=${left},top=${top}`
        );
        
        // Fonction pour vérifier si la popup est fermée
        const checkPopup = setInterval(() => {
          if (popup.closed) {
            clearInterval(checkPopup);
            // Vérifier si l'authentification a réussi en vérifiant le localStorage
            if (localStorage.getItem('token')) {
              // Émettre l'événement auth-changed
              window.dispatchEvent(new CustomEvent('auth-changed'));
              this.$router.push('/');
            }
          }
        }, 500);
        
      } catch (error) {
        console.error(`Erreur lors de la connexion avec ${provider}:`, error);
        this.errorMessage = `Une erreur est survenue lors de la connexion avec ${provider}.`;
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/auth/Login.vue << 'EOL'
<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Connexion à votre compte
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Ou
        <router-link to="/register" class="font-medium text-purple-600 hover:text-purple-500">
          créez un compte si vous n'en avez pas
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="login" class="space-y-6">
          <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-700">{{ errorMessage }}</p>
              </div>
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse e-mail
            </label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" required v-model="email"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input id="password" name="password" type="password" autocomplete="current-password" required v-model="password"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember-me" name="remember-me" type="checkbox" v-model="rememberMe"
                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Se souvenir de moi
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-purple-600 hover:text-purple-500">
                Mot de passe oublié ?
              </a>
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              :class="{ 'opacity-75 cursor-not-allowed': loading }"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Se connecter
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Ou continuer avec
              </span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-3 gap-3">
            <div>
              <a @click.prevent="socialLogin('facebook')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('google')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 12.25C6 11.57 6.13 10.9 6.37 10.28L2.58 7.5C1.89 8.91 1.5 10.47 1.5 12.12C1.5 13.8 1.93 15.41 2.69 16.85L6.39 13.92C6.14 13.4 6 12.85 6 12.25Z" fill="#FBBC05" />
                  <path d="M12.25 6.25C13.84 6.25 15.29 6.88 16.38 7.92L19.7 4.6C17.64 2.69 15.04 1.5 12.25 1.5C7.87 1.5 4.08 4.24 2.58 8.26L6.38 11.04C7.1 8.32 9.47 6.25 12.25 6.25Z" fill="#EA4335" />
                  <path d="M12.25 18.25C9.47 18.25 7.1 16.18 6.37 13.46L2.58 16.23C4.08 20.26 7.87 23 12.25 23C14.93 23 17.62 21.9 19.69 19.99L16.14 17.26C15.14 17.91 13.76 18.25 12.25 18.25Z" fill="#34A853" />
                  <path d="M22.5 12.25C22.5 11.5 22.39 10.69 22.25 10H12.25V14.25H18C17.75 15.75 16.83 16.9 15.53 17.64L19.08 20.37C21.14 18.35 22.5 15.58 22.5 12.25Z" fill="#4285F4" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('apple')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M12.7 10c0-2.8 2.3-4.1 2.3-4.1s-1.3-1.9-3.4-1.9c-1.5 0-2.5.8-3.3.8-.9 0-1.7-.7-3-.7-2.2 0-4.4 1.7-4.4 5.2 0 2.1.8 4.3 1.7 5.7.8 1.2 1.5 2.1 2.7 2.1 1 0 1.4-.6 2.9-.6s1.8.6 2.9.6c1.2 0 2-1.1 2.7-2.1.6-.9.9-1.7 1.1-2.4-1.3-.5-2.2-2-2.2-3.6zM11 5.1c.7-.8 1.8-1.3 2.7-1.4-2.3.4-3.5 2-3.5 2 .4-.1 1.2-.3 2-.3.4-.1.8-.1.8-.3z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      rememberMe: false,
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    async login() {
      this.loading = true;
      this.errorMessage = '';
      
      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password
        });
        
        // Stocker le token et les informations utilisateur dans le localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        // Configurer axios pour inclure le token dans les requêtes futures
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // Émettre un événement personnalisé pour notifier les autres composants
        window.dispatchEvent(new CustomEvent('auth-changed'));
        
        // Rediriger vers la page d'accueil
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur de connexion:', error);
        this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de la connexion.';
      } finally {
        this.loading = false;
      }
    },
    async socialLogin(provider) {
      try {
        // Récupérer l'URL de redirection
        const response = await axios.get(`/api/auth/${provider}`);
        
        // Ouvrir une fenêtre popup pour le processus d'authentification
        const width = 600;
        const height = 600;
        const left = (window.innerWidth / 2) - (width / 2);
        const top = (window.innerHeight / 2) - (height / 2);
        
        const popup = window.open(
          response.data.url,
          `${provider}Auth`,
          `width=${width},height=${height},left=${left},top=${top}`
        );
        
        // Fonction pour vérifier si la popup est fermée
        const checkPopup = setInterval(() => {
          if (popup.closed) {
            clearInterval(checkPopup);
            // Vérifier si l'authentification a réussi en vérifiant le localStorage
            if (localStorage.getItem('token')) {
              // Émettre l'événement auth-changed
              window.dispatchEvent(new CustomEvent('auth-changed'));
              this.$router.push('/');
            }
          }
        }, 500);
        
      } catch (error) {
        console.error(`Erreur lors de la connexion avec ${provider}:`, error);
        this.errorMessage = `Une erreur est survenue lors de la connexion avec ${provider}.`;
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/auth/Register.vue << 'EOL'
<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Créer un compte
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Ou
        <router-link to="/login" class="font-medium text-purple-600 hover:text-purple-500">
          connectez-vous si vous avez déjà un compte
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="register" class="space-y-6">
          <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-700" v-html="errorMessage"></p>
              </div>
            </div>
          </div>

          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
              Nom complet
            </label>
            <div class="mt-1">
              <input id="name" name="name" type="text" autocomplete="name" required v-model="name"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse e-mail
            </label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" required v-model="email"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input id="password" name="password" type="password" autocomplete="new-password" required v-model="password"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
            <p class="mt-1 text-xs text-gray-500">
              Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.
            </p>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
              Confirmer le mot de passe
            </label>
            <div class="mt-1">
              <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required v-model="passwordConfirmation"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="flex items-center">
            <input id="terms" name="terms" type="checkbox" required v-model="acceptTerms"
              class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
            />
            <label for="terms" class="ml-2 block text-sm text-gray-900">
              J'accepte les <a href="#" class="text-purple-600 hover:text-purple-500">conditions d'utilisation</a> et la <a href="#" class="text-purple-600 hover:text-purple-500">politique de confidentialité</a>
            </label>
          </div>

          <div>
            <button type="submit" :disabled="loading || !acceptTerms"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              :class="{ 'opacity-75 cursor-not-allowed': loading || !acceptTerms }"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Créer un compte
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Ou continuer avec
              </span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-3 gap-3">
            <div>
              <a @click.prevent="socialLogin('facebook')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('google')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 12.25C6 11.57 6.13 10.9 6.37 10.28L2.58 7.5C1.89 8.91 1.5 10.47 1.5 12.12C1.5 13.8 1.93 15.41 2.69 16.85L6.39 13.92C6.14 13.4 6 12.85 6 12.25Z" fill="#FBBC05" />
                  <path d="M12.25 6.25C13.84 6.25 15.29 6.88 16.38 7.92L19.7 4.6C17.64 2.69 15.04 1.5 12.25 1.5C7.87 1.5 4.08 4.24 2.58 8.26L6.38 11.04C7.1 8.32 9.47 6.25 12.25 6.25Z" fill="#EA4335" />
                  <path d="M12.25 18.25C9.47 18.25 7.1 16.18 6.37 13.46L2.58 16.23C4.08 20.26 7.87 23 12.25 23C14.93 23 17.62 21.9 19.69 19.99L16.14 17.26C15.14 17.91 13.76 18.25 12.25 18.25Z" fill="#34A853" />
                  <path d="M22.5 12.25C22.5 11.5 22.39 10.69 22.25 10H12.25V14.25H18C17.75 15.75 16.83 16.9 15.53 17.64L19.08 20.37C21.14 18.35 22.5 15.58 22.5 12.25Z" fill="#4285F4" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('apple')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M12.7 10c0-2.8 2.3-4.1 2.3-4.1s-1.3-1.9-3.4-1.9c-1.5 0-2.5.8-3.3.8-.9 0-1.7-.7-3-.7-2.2 0-4.4 1.7-4.4 5.2 0 2.1.8 4.3 1.7 5.7.8 1.2 1.5 2.1 2.7 2.1 1 0 1.4-.6 2.9-.6s1.8.6 2.9.6c1.2 0 2-1.1 2.7-2.1.6-.9.9-1.7 1.1-2.4-1.3-.5-2.2-2-2.2-3.6zM11 5.1c.7-.8 1.8-1.3 2.7-1.4-2.3.4-3.5 2-3.5 2 .4-.1 1.2-.3 2-.3.4-.1.8-.1.8-.3z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      passwordConfirmation: '',
      acceptTerms: false,
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    async register() {
      this.loading = true;
      this.errorMessage = '';
      
      if (this.password !== this.passwordConfirmation) {
        this.errorMessage = 'Les mots de passe ne correspondent pas.';
        this.loading = false;
        return;
      }
      
      try {
        const response = await axios.post('/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirmation
        });
        
        // Stocker le token et les informations utilisateur dans le localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        // Configurer axios pour inclure le token dans les requêtes futures
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // Émettre un événement personnalisé pour notifier les autres composants
        window.dispatchEvent(new CustomEvent('auth-changed'));
        
        // Afficher un message de succès et rediriger vers la page d'accueil
        alert(response.data.message);
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur d\'inscription:', error);
        if (error.response?.data?.errors) {
          // Formatter les erreurs de validation
          const errors = error.response.data.errors;
          let errorMsg = '<ul class="list-disc list-inside">';
          for (const field in errors) {
            errors[field].forEach(error => {
              errorMsg += `<li>${error}</li>`;
            });
          }
          errorMsg += '</ul>';
          this.errorMessage = errorMsg;
        } else {
          this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de l\'inscription.';
        }
      } finally {
        this.loading = false;
      }
    },
    async socialLogin(provider) {
      try {
        // Récupérer l'URL de redirection
        const response = await axios.get(`/api/auth/${provider}`);
        
        // Ouvrir une fenêtre popup pour le processus d'authentification
        const width = 600;
        const height = 600;
        const left = (window.innerWidth / 2) - (width / 2);
        const top = (window.innerHeight / 2) - (height / 2);
        
        const popup = window.open(
          response.data.url,
          `${provider}Auth`,
          `width=${width},height=${height},left=${left},top=${top}`
        );
        
        // Fonction pour vérifier si la popup est fermée
        const checkPopup = setInterval(() => {
          if (popup.closed) {
            clearInterval(checkPopup);
            // Vérifier si l'authentification a réussi en vérifiant le localStorage
            if (localStorage.getItem('token')) {
              // Émettre l'événement auth-changed
              window.dispatchEvent(new CustomEvent('auth-changed'));
              this.$router.push('/');
            }
          }
        }, 500);
        
      } catch (error) {
        console.error(`Erreur lors de la connexion avec ${provider}:`, error);
        this.errorMessage = `Une erreur est survenue lors de la connexion avec ${provider}.`;
      }
    }
  }
};
</script>
EOL

ADMIN
cat > routes/web.php << 'EOL'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\UserController;

// Routes API avec préfixe explicite
Route::prefix('api')->group(function () {
    // Routes d'authentification
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    
    // Routes protégées par authentification
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/user', [AuthController::class, 'updateProfile']);
        
        // Routes pour les commandes de l'utilisateur connecté
        Route::get('/user/orders', [OrderController::class, 'index']);
        
        // Routes d'administration protégées par le rôle admin
        Route::middleware('role:admin')->group(function () {
            // Dashboard
            Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
            
            // Gestion des utilisateurs
            Route::get('/admin/users', [UserController::class, 'index']);
            Route::get('/admin/users/{user}', [UserController::class, 'show']);
            Route::put('/admin/users/{user}', [UserController::class, 'update']);
            Route::delete('/admin/users/{user}', [UserController::class, 'destroy']);
            
            // Gestion des produits
            Route::get('/admin/products', [ProductController::class, 'adminIndex']);
            Route::post('/admin/products', [ProductController::class, 'store']);
            Route::get('/admin/products/{product}', [ProductController::class, 'show']);
            Route::put('/admin/products/{product}', [ProductController::class, 'update']);
            Route::delete('/admin/products/{product}', [ProductController::class, 'destroy']);
            
            // Gestion des commandes
            Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
            Route::get('/admin/orders/{order}', [OrderController::class, 'adminShow']);
            Route::put('/admin/orders/{order}', [OrderController::class, 'adminUpdate']);
            
            // Gestion des catégories
            Route::get('/admin/categories', [CategoryController::class, 'index']);
            Route::post('/admin/categories', [CategoryController::class, 'store']);
            Route::put('/admin/categories/{category}', [CategoryController::class, 'update']);
            Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy']);
        });
    });
    
    // Routes pour les produits
    Route::apiResource('products', ProductController::class);
    
    // Routes pour les commandes
    Route::apiResource('orders', OrderController::class);
    
    // Route spécifique pour créer une commande depuis le live
    Route::post('orders/live', [OrderController::class, 'createLiveOrder']);
    
    // Route pour obtenir les produits disponibles en live
    Route::get('products/live', [ProductController::class, 'getLiveProducts']);
    
    // Route pour mettre à jour le stock d'un produit
    Route::patch('products/{product}/stock', [ProductController::class, 'updateStock']);
});

// Routes Web pour servir l'application Vue.js
Route::get('/', function () {
    return view('app');
});

// Catch-all route for SPA
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
EOL

php artisan make:controller API/AdminController

cat > app/Http/Controllers/API/AdminController.php << 'EOL'
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function dashboard(Request $request)
    {
        // Statistiques générales
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'paid')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'recent_orders' => Order::with(['user', 'items.product'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'low_stock_products' => DB::table('product_size')
                ->join('products', 'products.id', '=', 'product_size.product_id')
                ->join('sizes', 'sizes.id', '=', 'product_size.size_id')
                ->where('product_size.stock', '<', 5)
                ->select(
                    'products.id',
                    'products.name as product_name',
                    'sizes.name as size_name',
                    'product_size.stock'
                )
                ->get(),
            'monthly_revenue' => Order::selectRaw('
                    MONTH(created_at) as month,
                    YEAR(created_at) as year,
                    SUM(total_amount) as total
                ')
                ->where('status', 'paid')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month', 'year')
                ->orderBy('month')
                ->get(),
        ];

        return response()->json($stats);
    }
}
EOL


php artisan make:controller API/UserController

cat > app/Http/Controllers/API/UserController.php << 'EOL'
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtrage par recherche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtrage par rôle
        if ($request->has('is_admin')) {
            $query->where('is_admin', $request->is_admin === 'true');
        }

        $users = $query->with('orders')->paginate(10);

        return response()->json($users);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['orders.items.product']);
        
        return response()->json($user);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'is_admin' => 'boolean',
            'email_verified' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userData = $request->only(['name', 'email', 'is_admin', 'email_verified']);
        
        if ($request->has('password') && $request->password) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return response()->json($user);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Ne pas supprimer le compte admin principal
        if ($user->email === 'admin@dressingdespiplettes.com') {
            return response()->json(['message' => 'Impossible de supprimer le compte administrateur principal.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }
}
EOL

php artisan make:controller API/CategoryController

cat > app/Http/Controllers/API/CategoryController.php << 'EOL'
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
EOL

cat > app/Http/Controllers/API/ProductController.php << 'EOL'
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
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtrage par catégorie
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrage par disponibilité
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        if ($request->has('is_live_available')) {
            $query->where('is_live_available', $request->is_live_available === 'true');
        }

        // Filtrage par stock
        if ($request->has('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereHas('sizes', function ($q) {
                    $q->where('stock', '<', 5);
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
EOL

cat > app/Http/Controllers/API/OrderController.php << 'EOL'
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
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('id', '=', $search);
            });
        }

        // Filtrage par statut
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filtrage par type de commande
        if ($request->has('is_live_order')) {
            $query->where('is_live_order', $request->is_live_order === 'true');
        }

        // Filtrage par date
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
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
EOL

mkdir -p resources/js/layouts
cat > resources/js/layouts/AdminLayout.vue << 'EOL'
<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800">
      <div class="flex flex-col h-full">
        <div class="flex items-center justify-center h-16 bg-gray-900">
          <h1 class="text-white text-lg font-semibold">Administration</h1>
        </div>
        
        <nav class="flex-1 px-4 py-4 space-y-2">
          <router-link
            to="/admin/dashboard"
            class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
            :class="{ 'bg-gray-700 text-white': $route.path === '/admin/dashboard' }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Dashboard
          </router-link>
          
          <router-link
            to="/admin/products"
            class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
            :class="{ 'bg-gray-700 text-white': $route.path.startsWith('/admin/products') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            Produits
          </router-link>
          
          <router-link
            to="/admin/orders"
            class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
            :class="{ 'bg-gray-700 text-white': $route.path.startsWith('/admin/orders') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            Commandes
          </router-link>
          
          <router-link
            to="/admin/users"
            class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
            :class="{ 'bg-gray-700 text-white': $route.path.startsWith('/admin/users') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            Utilisateurs
          </router-link>
          
          <router-link
            to="/admin/categories"
            class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
            :class="{ 'bg-gray-700 text-white': $route.path.startsWith('/admin/categories') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            Catégories
          </router-link>
        </nav>
        
        <div class="p-4">
          <router-link
            to="/"
            class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
            </svg>
            Retour au site
          </router-link>
        </div>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="bg-white shadow-sm">
        <div class="flex items-center justify-between h-16 px-6">
          <h2 class="text-xl font-semibold text-gray-800">{{ pageTitle }}</h2>
          
          <div class="flex items-center space-x-4">
            <span class="text-gray-600">{{ user.name }}</span>
            <button
              @click="logout"
              class="text-gray-500 hover:text-gray-700"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
            </button>
          </div>
        </div>
      </header>
      
      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-6">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminLayout',
  data() {
    return {
      user: {}
    };
  },
  computed: {
    pageTitle() {
      const titles = {
        '/admin/dashboard': 'Dashboard',
        '/admin/products': 'Gestion des produits',
        '/admin/orders': 'Gestion des commandes',
        '/admin/users': 'Gestion des utilisateurs',
        '/admin/categories': 'Gestion des catégories'
      };
      
      // Chercher une correspondance exacte d'abord
      if (titles[this.$route.path]) {
        return titles[this.$route.path];
      }
      
      // Ensuite chercher une correspondance partielle
      for (const path in titles) {
        if (this.$route.path.startsWith(path)) {
          return titles[path];
        }
      }
      
      return 'Administration';
    }
  },
  created() {
    this.loadUser();
  },
  methods: {
    loadUser() {
      const userJson = localStorage.getItem('user');
      if (userJson) {
        this.user = JSON.parse(userJson);
      }
    },
    async logout() {
      try {
        await axios.post('/api/logout');
        
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        
        delete axios.defaults.headers.common['Authorization'];
        
        window.dispatchEvent(new CustomEvent('auth-changed'));
        
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur lors de la déconnexion:', error);
        
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        delete axios.defaults.headers.common['Authorization'];
        window.dispatchEvent(new CustomEvent('auth-changed'));
        this.$router.push('/');
      }
    }
  }
};
</script>
EOL
 
mkdir -p resources/js/views/admin
cat > resources/js/views/admin/AdminDashboard.vue << 'EOL'
<template>
  <div class="admin-dashboard">
    <div v-if="loading" class="text-center py-8">
      <div class="inline-flex items-center">
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Chargement des statistiques...
      </div>
    </div>
    
    <div v-else>
      <!-- Statistiques générales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Utilisateurs</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_users }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Produits</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_products }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Commandes</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_orders }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Chiffre d'affaires</p>
              <p class="text-2xl font-semibold text-gray-700">{{ formatCurrency(stats.total_revenue) }}</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Commandes récentes et Produits en rupture -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Commandes récentes -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Commandes récentes</h3>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div v-for="order in stats.recent_orders" :key="order.id" class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">#{{ order.id }} - {{ order.customer_name }}</p>
                  <p class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">{{ formatCurrency(order.total_amount) }}</p>
                  <span :class="getStatusClass(order.status)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                    {{ getStatusLabel(order.status) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Produits en rupture de stock -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Stock faible</h3>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div v-for="product in stats.low_stock_products" :key="`${product.id}-${product.size_name}`" class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ product.product_name }}</p>
                  <p class="text-xs text-gray-500">Taille: {{ product.size_name }}</p>
                </div>
                <div class="text-right">
                  <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                    Stock: {{ product.stock }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Graphique des revenus mensuels -->
      <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Revenus mensuels</h3>
        <div class="h-64 flex items-center justify-center text-gray-500">
          <!-- Vous pouvez intégrer ici un graphique avec Chart.js ou une autre librairie -->
          <p>Graphique des revenus mensuels (à implémenter)</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminDashboard',
  data() {
    return {
      loading: true,
      stats: {
        total_users: 0,
        total_products: 0,
        total_orders: 0,
        total_revenue: 0,
        pending_orders: 0,
        recent_orders: [],
        low_stock_products: [],
        monthly_revenue: []
      }
    };
  },
  created() {
    this.loadDashboardStats();
  },
  methods: {
    async loadDashboardStats() {
      try {
        const response = await axios.get('/api/admin/dashboard');
        this.stats = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
        alert('Impossible de charger les statistiques');
      } finally {
        this.loading = false;
      }
    },
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount);
    },
    formatDate(dateString) {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(new Date(dateString));
    },
    getStatusClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    },
    getStatusLabel(status) {
      const labels = {
        pending: 'En attente',
        confirmed: 'Confirmée',
        paid: 'Payée',
        shipped: 'Expédiée',
        delivered: 'Livrée',
        cancelled: 'Annulée'
      };
      return labels[status] || status;
    }
  }
};
</script>
EOL

cat > resources/js/views/admin/AdminProducts.vue << 'EOL'
<template>
  <div class="admin-products">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-800">Gestion des produits</h2>
      <router-link
        to="/admin/products/new"
        class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
      >
        Ajouter un produit
      </router-link>
    </div>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Nom, référence..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @input="loadProducts"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
          <select
            v-model="filters.category_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadProducts"
          >
            <option value="">Toutes les catégories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
          <select
            v-model="filters.is_active"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadProducts"
          >
            <option value="">Tous les statuts</option>
            <option value="true">Actif</option>
            <option value="false">Inactif</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
          <select
            v-model="filters.stock_status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadProducts"
          >
            <option value="">Tous</option>
            <option value="low">Stock faible</option>
            <option value="out">Rupture de stock</option>
          </select>
        </div>
      </div>
    </div>
    
    <!-- Liste des produits -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Produit
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Référence
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Catégorie
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Prix
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Stock
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Statut
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in products" :key="product.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 flex-shrink-0">
                  <img class="h-10 w-10 rounded-full object-cover" :src="product.image_url" :alt="product.name">
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ product.reference }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ product.category ? product.category.name : '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatCurrency(product.price) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <div v-if="product.sizes && product.sizes.length > 0">
                <span v-for="size in product.sizes" :key="size.id" class="inline-block mr-2">
                  {{ size.name }}: {{ size.pivot.stock }}
                </span>
              </div>
              <span v-else>-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ product.is_active ? 'Actif' : 'Inactif' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <router-link
                :to="`/admin/products/${product.id}/edit`"
                class="text-indigo-600 hover:text-indigo-900 mr-2"
              >
                Modifier
              </router-link>
              <button
                @click="deleteProduct(product)"
                class="text-red-600 hover:text-red-900"
              >
                Supprimer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Précédent
          </button>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Suivant
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Affichage de
              <span class="font-medium">{{ pagination.from }}</span>
              à
              <span class="font-medium">{{ pagination.to }}</span>
              sur
              <span class="font-medium">{{ pagination.total }}</span>
              résultats
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Précédent</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
              
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="changePage(page)"
                :class="[
                  page === pagination.current_page
                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                ]"
              >
                {{ page }}
              </button>
              
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Suivant</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminProducts',
  data() {
    return {
      products: [],
      categories: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      },
      filters: {
        search: '',
        category_id: '',
        is_active: '',
        stock_status: ''
      },
      loading: false
    };
  },
  computed: {
    visiblePages() {
      const pages = [];
      const current = this.pagination.current_page;
      const last = this.pagination.last_page;
      
      let start = Math.max(1, current - 2);
      let end = Math.min(last, current + 2);
      
      if (current <= 3) {
        end = Math.min(5, last);
      }
      
      if (current >= last - 2) {
        start = Math.max(1, last - 4);
      }
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    }
  },
  created() {
    this.loadCategories();
    this.loadProducts();
  },
  methods: {
    async loadCategories() {
      try {
        const response = await axios.get('/api/admin/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des catégories:', error);
      }
    },
    async loadProducts(page = 1) {
      this.loading = true;
      try {
        const params = {
          page,
          ...this.filters
        };
        
        const response = await axios.get('/api/admin/products', { params });
        this.products = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page,
          total: response.data.total,
          from: response.data.from,
          to: response.data.to
        };
      } catch (error) {
        console.error('Erreur lors du chargement des produits:', error);
        alert('Impossible de charger les produits');
      } finally {
        this.loading = false;
      }
    },
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.loadProducts(page);
      }
    },
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount);
    },
    async deleteProduct(product) {
      if (!confirm(`Êtes-vous sûr de vouloir supprimer le produit "${product.name}" ?`)) {
        return;
      }
      
      try {
        await axios.delete(`/api/admin/products/${product.id}`);
        this.loadProducts(this.pagination.current_page);
        alert('Produit supprimé avec succès');
      } catch (error) {
        console.error('Erreur lors de la suppression du produit:', error);
        alert('Impossible de supprimer le produit');
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/admin/AdminProductEdit.vue << 'EOL'
<template>
  <div class="admin-product-edit">
    <div class="max-w-4xl mx-auto">
      <div class="mb-6">
        <router-link
          to="/admin/products"
          class="text-gray-600 hover:text-gray-900"
        >
          ← Retour aux produits
        </router-link>
      </div>
      
      <h2 class="text-2xl font-semibold text-gray-800 mb-6">
        {{ isNew ? 'Nouveau produit' : 'Modifier le produit' }}
      </h2>
      
      <form @submit.prevent="saveProduct" class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Informations générales -->
          <div>
            <h3 class="text-lg font-medium text-gray-700 mb-4">Informations générales</h3>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Nom du produit</label>
              <input
                v-model="product.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md"
              >
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea
                v-model="product.description"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md"
              ></textarea>
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Prix</label>
              <input
                v-model.number="product.price"
                type="number"
                step="0.01"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md"
              >
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Référence</label>
              <input
                v-model="product.reference"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md"
              >
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">URL de l'image</label>
              <input
                v-model="product.image_url"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md"
              >
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
              <select
                v-model="product.category_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md"
              >
                <option value="">Sans catégorie</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
          </div>
          
          <!-- Options et stock -->
          <div>
            <h3 class="text-lg font-medium text-gray-700 mb-4">Options et stock</h3>
            
            <div class="mb-4">
              <label class="flex items-center">
                <input
                  v-model="product.is_active"
                  type="checkbox"
                  class="rounded text-purple-600"
                >
                <span class="ml-2 text-sm text-gray-700">Produit actif</span>
              </label>
            </div>
            
            <div class="mb-6">
              <label class="flex items-center">
                <input
                  v-model="product.is_live_available"
                  type="checkbox"
                  class="rounded text-purple-600"
                >
                <span class="ml-2 text-sm text-gray-700">Disponible pour les lives</span>
              </label>
            </div>
            
            <h4 class="text-md font-medium text-gray-700 mb-3">Stock par taille</h4>
            <div class="space-y-2">
              <div v-for="size in availableSizes" :key="size.id" class="flex items-center">
                <label class="w-16 text-sm text-gray-700">{{ size.name }}</label>
                <input
                  v-model.number="getOrCreateSizeStock(size.id).stock"
                  type="number"
                  min="0"
                  class="w-20 px-3 py-1 border border-gray-300 rounded-md"
                >
              </div>
            </div>
          </div>
        </div>
        
        <!-- Boutons d'action -->
        <div class="mt-6 flex justify-end space-x-3">
          <router-link
            to="/admin/products"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Annuler
          </router-link>
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminProductEdit',
  data() {
    return {
      product: {
        name: '',
        description: '',
        price: 0,
        reference: '',
        image_url: '',
        category_id: null,
        is_active: true,
        is_live_available: true,
        sizes: []
      },
      categories: [],
      availableSizes: [],
      loading: false
    };
  },
  computed: {
    isNew() {
      return !this.$route.params.id || this.$route.params.id === 'new';
    }
  },
  created() {
    this.loadCategories();
    this.loadSizes();
    if (!this.isNew) {
      this.loadProduct();
    }
  },
  methods: {
    async loadCategories() {
      try {
        const response = await axios.get('/api/admin/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des catégories:', error);
      }
    },
    async loadSizes() {
      try {
        // Normalement, vous auriez une route API pour récupérer les tailles
        // Pour l'instant, utilisons des données statiques
        this.availableSizes = [
          { id: 1, name: 'XS' },
          { id: 2, name: 'S' },
          { id: 3, name: 'M' },
          { id: 4, name: 'L' },
          { id: 5, name: 'XL' },
          { id: 6, name: 'XXL' }
        ];
      } catch (error) {
        console.error('Erreur lors du chargement des tailles:', error);
      }
    },
    async loadProduct() {
      try {
        const response = await axios.get(`/api/admin/products/${this.$route.params.id}`);
        this.product = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement du produit:', error);
        alert('Impossible de charger le produit');
        this.$router.push('/admin/products');
      }
    },
    getOrCreateSizeStock(sizeId) {
      let sizeStock = this.product.sizes.find(s => s.id === sizeId);
      if (!sizeStock) {
        sizeStock = { id: sizeId, pivot: { stock: 0 } };
        this.product.sizes.push(sizeStock);
      }
      return sizeStock.pivot;
    },
    async saveProduct() {
      this.loading = true;
      try {
        const data = {
          ...this.product,
          sizes: this.product.sizes
            .filter(s => s.pivot.stock > 0)
            .map(s => ({ id: s.id, stock: s.pivot.stock }))
        };
        
        if (this.isNew) {
          await axios.post('/api/admin/products', data);
          alert('Produit créé avec succès');
        } else {
          await axios.put(`/api/admin/products/${this.product.id}`, data);
          alert('Produit mis à jour avec succès');
        }
        
        this.$router.push('/admin/products');
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement du produit:', error);
        alert('Impossible d\'enregistrer le produit');
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/admin/AdminOrders.vue << 'EOL'
<template>
  <div class="admin-orders">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-800">Gestion des commandes</h2>
    </div>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="N°, nom, email..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @input="loadOrders"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
          <select
            v-model="filters.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadOrders"
          >
            <option value="">Tous les statuts</option>
            <option value="pending">En attente</option>
            <option value="confirmed">Confirmée</option>
            <option value="paid">Payée</option>
            <option value="shipped">Expédiée</option>
            <option value="delivered">Livrée</option>
            <option value="cancelled">Annulée</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date de</label>
          <input
            v-model="filters.date_from"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadOrders"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date à</label>
          <input
            v-model="filters.date_to"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadOrders"
          >
        </div>
      </div>
    </div>
    
    <!-- Liste des commandes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              N° Commande
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Client
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Date
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Total
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Statut
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Type
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in orders" :key="order.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              #{{ order.id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ order.customer_name }}</div>
              <div class="text-sm text-gray-500">{{ order.customer_email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(order.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatCurrency(order.total_amount) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <select
                v-model="order.status"
                @change="updateOrderStatus(order)"
                :class="getStatusClass(order.status)"
                class="text-xs font-semibold rounded-full px-3 py-1"
              >
                <option value="pending">En attente</option>
                <option value="confirmed">Confirmée</option>
                <option value="paid">Payée</option>
                <option value="shipped">Expédiée</option>
                <option value="delivered">Livrée</option>
                <option value="cancelled">Annulée</option>
              </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span v-if="order.is_live_order" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                Live
              </span>
              <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                Web
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <router-link
                :to="`/admin/orders/${order.id}`"
                class="text-indigo-600 hover:text-indigo-900"
              >
                Détails
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Précédent
          </button>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Suivant
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Affichage de
              <span class="font-medium">{{ pagination.from }}</span>
              à
              <span class="font-medium">{{ pagination.to }}</span>
              sur
              <span class="font-medium">{{ pagination.total }}</span>
              résultats
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="changePage(1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Première
              </button>
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                &lt;
              </button>
              <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                Page {{ pagination.current_page }} sur {{ pagination.last_page }}
              </span>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                &gt;
              </button>
              <button
                @click="changePage(pagination.last_page)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Dernière
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminOrders',
  data() {
    return {
      orders: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      },
      filters: {
        search: '',
        status: '',
        date_from: '',
        date_to: '',
        is_live_order: ''
      },
      loading: false
    };
  },
  created() {
    this.loadOrders();
  },
  methods: {
    async loadOrders(page = 1) {
      this.loading = true;
      try {
        const params = {
          page,
          ...this.filters
        };
        
        const response = await axios.get('/api/admin/orders', { params });
        this.orders = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page,
          total: response.data.total,
          from: response.data.from,
          to: response.data.to
        };
      } catch (error) {
        console.error('Erreur lors du chargement des commandes:', error);
        alert('Impossible de charger les commandes');
      } finally {
        this.loading = false;
      }
    },
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.loadOrders(page);
      }
    },
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount);
    },
    formatDate(dateString) {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(new Date(dateString));
    },
    getStatusClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    },
    async updateOrderStatus(order) {
      try {
        await axios.put(`/api/admin/orders/${order.id}`, {
          status: order.status
        });
        alert('Statut de la commande mis à jour');
      } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
        alert('Impossible de mettre à jour le statut');
        // Recharger pour restaurer le statut original
        this.loadOrders(this.pagination.current_page);
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/admin/AdminOrderDetails.vue << 'EOL'
<template>
  <div class="admin-order-details">
    <div class="mb-6">
      <router-link
        to="/admin/orders"
        class="text-gray-600 hover:text-gray-900"
      >
        ← Retour aux commandes
      </router-link>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <div class="inline-flex items-center">
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Chargement de la commande...
      </div>
    </div>
    
    <div v-else-if="order" class="max-w-6xl mx-auto">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6">Commande #{{ order.id }}</h2>
      
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations de la commande -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Détails de la commande</h3>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div>
                <p class="text-sm text-gray-600">Date de commande</p>
                <p class="font-medium">{{ formatDate(order.created_at) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Type de commande</p>
                <span v-if="order.is_live_order" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                  Commande Live
                </span>
                <span v-else class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                  Commande Web
                </span>
              </div>
              <div>
                <p class="text-sm text-gray-600">Statut</p>
                <select
                  v-model="order.status"
                  @change="updateOrderStatus"
                  :class="getStatusClass(order.status)"
                  class="mt-1 text-sm font-semibold rounded px-3 py-1"
                >
                  <option value="pending">En attente</option>
                  <option value="confirmed">Confirmée</option>
                  <option value="paid">Payée</option>
                  <option value="shipped">Expédiée</option>
                  <option value="delivered">Livrée</option>
                  <option value="cancelled">Annulée</option>
                </select>
              </div>
              <div>
                <p class="text-sm text-gray-600">Total</p>
                <p class="font-medium text-lg">{{ formatCurrency(order.total_amount) }}</p>
              </div>
            </div>
            
            <div v-if="order.notes" class="border-t pt-4">
              <p class="text-sm text-gray-600 mb-1">Notes</p>
              <p class="text-gray-800">{{ order.notes }}</p>
            </div>
          </div>
          
          <!-- Articles de la commande -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Articles commandés</h3>
            
            <div class="space-y-4">
              <div v-for="item in order.items" :key="item.id" class="flex items-center space-x-4 pb-4 border-b last:border-0">
                <img
                  :src="item.product.image_url"
                  :alt="item.product.name"
                  class="w-16 h-16 object-cover rounded"
                >
                <div class="flex-1">
                  <h4 class="font-medium">{{ item.product.name }}</h4>
                  <p class="text-sm text-gray-600">
                    Taille: {{ item.size.name }} | Quantité: {{ item.quantity }}
                  </p>
                </div>
                <div class="text-right">
                  <p class="font-medium">{{ formatCurrency(item.price * item.quantity) }}</p>
                  <p class="text-sm text-gray-600">{{ formatCurrency(item.price) }} / pièce</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Informations client -->
        <div>
          <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Informations client</h3>
            
            <div class="space-y-3">
              <div>
                <p class="text-sm text-gray-600">Nom</p>
                <p class="font-medium">{{ order.customer_name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Email</p>
                <p class="font-medium">{{ order.customer_email }}</p>
              </div>
              <div v-if="order.customer_phone">
                <p class="text-sm text-gray-600">Téléphone</p>
                <p class="font-medium">{{ order.customer_phone }}</p>
              </div>
              <div v-if="order.customer_address">
                <p class="text-sm text-gray-600">Adresse</p>
                <p class="font-medium">{{ order.customer_address }}</p>
              </div>
              <div v-if="order.user">
                <p class="text-sm text-gray-600">Compte client</p>
                <router-link :to="`/admin/users/${order.user.id}`" class="font-medium text-purple-600 hover:text-purple-800">
                  {{ order.user.name }}
                </router-link>
              </div>
            </div>
          </div>
          
          <!-- Actions -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Actions</h3>
            
            <div class="space-y-3">
              <button
                v-if="order.status === 'pending'"
                @click="confirmOrder"
                class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
              >
                Confirmer la commande
              </button>
              
              <button
                v-if="order.status === 'confirmed'"
                @click="markAsPaid"
                class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
              >
                Marquer comme payée
              </button>
              
              <button
                v-if="order.status === 'paid'"
                @click="markAsShipped"
                class="w-full bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700"
              >
                Marquer comme expédiée
              </button>
              
              <button
                @click="printOrder"
                class="w-full bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"
              >
                Imprimer la commande
              </button>
              
              <button
                v-if="canCancel"
                @click="cancelOrder"
                class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
              >
                Annuler la commande
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminOrderDetails',
  data() {
    return {
      order: null,
      loading: true
    };
  },
  computed: {
    canCancel() {
      return this.order && ['pending', 'confirmed'].includes(this.order.status);
    }
  },
  created() {
    this.loadOrder();
  },
  methods: {
    async loadOrder() {
      try {
        const response = await axios.get(`/api/admin/orders/${this.$route.params.id}`);
        this.order = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement de la commande:', error);
        alert('Impossible de charger la commande');
        this.$router.push('/admin/orders');
      } finally {
        this.loading = false;
      }
    },
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount);
    },
    formatDate(dateString) {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(new Date(dateString));
    },
    getStatusClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    },
    async updateOrderStatus() {
      try {
        await axios.put(`/api/admin/orders/${this.order.id}`, {
          status: this.order.status
        });
        alert('Statut de la commande mis à jour');
      } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
        alert('Impossible de mettre à jour le statut');
        this.loadOrder();
      }
    },
    async updateOrderWithStatus(status) {
      try {
        await axios.put(`/api/admin/orders/${this.order.id}`, { status });
        this.order.status = status;
        alert(`Commande marquée comme ${this.getStatusLabel(status)}`);
      } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
        alert('Impossible de mettre à jour le statut');
      }
    },
    confirmOrder() {
      this.updateOrderWithStatus('confirmed');
    },
    markAsPaid() {
      this.updateOrderWithStatus('paid');
    },
    markAsShipped() {
      this.updateOrderWithStatus('shipped');
    },
    cancelOrder() {
      if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
        this.updateOrderWithStatus('cancelled');
      }
    },
    printOrder() {
      window.print();
    },
    getStatusLabel(status) {
      const labels = {
        pending: 'en attente',
        confirmed: 'confirmée',
        paid: 'payée',
        shipped: 'expédiée',
        delivered: 'livrée',
        cancelled: 'annulée'
      };
      return labels[status] || status;
    }
  }
};
</script>
EOL

cat > resources/js/views/admin/AdminUsers.vue << 'EOL'
<template>
  <div class="admin-users">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-800">Gestion des utilisateurs</h2>
    </div>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Nom, email..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @input="loadUsers"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
          <select
            v-model="filters.is_admin"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadUsers"
          >
            <option value="">Tous les utilisateurs</option>
            <option value="true">Administrateurs</option>
            <option value="false">Clients</option>
          </select>
        </div>
      </div>
    </div>
    
    <!-- Liste des utilisateurs -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Utilisateur
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Type
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Email vérifié
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Date d'inscription
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Commandes
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="user.is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'" 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ user.is_admin ? 'Admin' : 'Client' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="user.email_verified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ user.email_verified ? 'Vérifié' : 'Non vérifié' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ user.orders ? user.orders.length : 0 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="editUser(user)"
                class="text-indigo-600 hover:text-indigo-900 mr-2"
              >
                Modifier
              </button>
              <button
                v-if="user.email !== 'admin@dressingdespiplettes.com'"
                @click="deleteUser(user)"
                class="text-red-600 hover:text-red-900"
              >
                Supprimer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Précédent
          </button>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Suivant
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Affichage de
              <span class="font-medium">{{ pagination.from }}</span>
              à
              <span class="font-medium">{{ pagination.to }}</span>
              sur
              <span class="font-medium">{{ pagination.total }}</span>
              résultats
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="changePage(1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Première
              </button>
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                &lt;
              </button>
              <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                Page {{ pagination.current_page }} sur {{ pagination.last_page }}
              </span>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                &gt;
              </button>
              <button
                @click="changePage(pagination.last_page)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Dernière
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal d'édition -->
    <div v-if="editingUser" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="editingUser = null"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="saveUser">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Modifier l'utilisateur
              </h3>
              
              <div class="space-y-4">
                <div>
                  <label for="edit-name" class="block text-sm font-medium text-gray-700">Nom</label>
                  <input
                    id="edit-name"
                    v-model="editingUser.name"
                    type="text"
                    required
                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md"
                  >
                </div>
                
                <div>
                  <label for="edit-email" class="block text-sm font-medium text-gray-700">Email</label>
                  <input
                    id="edit-email"
                    v-model="editingUser.email"
                    type="email"
                    required
                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md"
                  >
                </div>
                
                <div>
                  <label for="edit-password" class="block text-sm font-medium text-gray-700">
                    Nouveau mot de passe (laisser vide pour ne pas changer)
                  </label>
                  <input
                    id="edit-password"
                    v-model="editingUser.password"
                    type="password"
                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md"
                  >
                </div>
                
                <div class="flex items-center">
                  <input
                    id="edit-is-admin"
                    v-model="editingUser.is_admin"
                    type="checkbox"
                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                  >
                  <label for="edit-is-admin" class="ml-2 block text-sm text-gray-900">
                    Administrateur
                  </label>
                </div>
                
                <div class="flex items-center">
                  <input
                    id="edit-email-verified"
                    v-model="editingUser.email_verified"
                    type="checkbox"
                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                  >
                  <label for="edit-email-verified" class="ml-2 block text-sm text-gray-900">
                    Email vérifié
                  </label>
                </div>
              </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Enregistrer
              </button>
              <button
                type="button"
                @click="editingUser = null"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Annuler
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminUsers',
  data() {
    return {
      users: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      },
      filters: {
        search: '',
        is_admin: ''
      },
      loading: false,
      editingUser: null
    };
  },
  created() {
    this.loadUsers();
  },
  methods: {
    async loadUsers(page = 1) {
      this.loading = true;
      try {
        const params = {
          page,
          ...this.filters
        };
        
        const response = await axios.get('/api/admin/users', { params });
        this.users = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page,
          total: response.data.total,
          from: response.data.from,
          to: response.data.to
        };
      } catch (error) {
        console.error('Erreur lors du chargement des utilisateurs:', error);
        alert('Impossible de charger les utilisateurs');
      } finally {
        this.loading = false;
      }
    },
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.loadUsers(page);
      }
    },
    formatDate(dateString) {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      }).format(new Date(dateString));
    },
    editUser(user) {
      this.editingUser = {
        ...user,
        password: ''
      };
    },
    async saveUser() {
      try {
        const data = {
          name: this.editingUser.name,
          email: this.editingUser.email,
          is_admin: this.editingUser.is_admin,
          email_verified: this.editingUser.email_verified
        };
        
        if (this.editingUser.password) {
          data.password = this.editingUser.password;
        }
        
        await axios.put(`/api/admin/users/${this.editingUser.id}`, data);
        
        alert('Utilisateur mis à jour avec succès');
        this.editingUser = null;
        this.loadUsers(this.pagination.current_page);
      } catch (error) {
        console.error('Erreur lors de la mise à jour de l\'utilisateur:', error);
        alert('Impossible de mettre à jour l\'utilisateur');
      }
    },
    async deleteUser(user) {
      if (!confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${user.name}" ?`)) {
        return;
      }
      
      try {
        await axios.delete(`/api/admin/users/${user.id}`);
        this.loadUsers(this.pagination.current_page);
        alert('Utilisateur supprimé avec succès');
      } catch (error) {
        console.error('Erreur lors de la suppression de l\'utilisateur:', error);
        if (error.response && error.response.status === 403) {
          alert('Impossible de supprimer cet utilisateur. Il s\'agit peut-être du compte administrateur principal.');
        } else {
          alert('Impossible de supprimer l\'utilisateur');
        }
      }
    }
  }
};
</script>
EOL

cat > resources/js/views/admin/AdminCategories.vue << 'EOL'
<template>
  <div class="admin-categories">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-800">Gestion des catégories</h2>
      <button
        @click="showAddModal"
        class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
      >
        Ajouter une catégorie
      </button>
    </div>
    
    <!-- Liste des catégories -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Nom
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Description
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Nombre de produits
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Statut
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="category in categories" :key="category.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-500">{{ category.description || '-' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ category.products_count || 0 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ category.is_active ? 'Actif' : 'Inactif' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="editCategory(category)"
                class="text-indigo-600 hover:text-indigo-900 mr-2"
              >
                Modifier
              </button>
              <button
                @click="deleteCategory(category)"
                class="text-red-600 hover:text-red-900"
              >
                Supprimer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Modal d'ajout/édition -->
    <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="saveCategory">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                {{ editingCategory.id ? 'Modifier la catégorie' : 'Ajouter une catégorie' }}
              </h3>
              
              <div class="space-y-4">
                <div>
                  <label for="category-name" class="block text-sm font-medium text-gray-700">Nom</label>
                  <input
                    id="category-name"
                    v-model="editingCategory.name"
                    type="text"
                    required
                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md"
                  >
                </div>
                
                <div>
                  <label for="category-description" class="block text-sm font-medium text-gray-700">Description</label>
                  <textarea
                    id="category-description"
                    v-model="editingCategory.description"
                    rows="3"
                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md"
                  ></textarea>
                </div>
                
                <div class="flex items-center">
                  <input
                    id="category-active"
                    v-model="editingCategory.is_active"
                    type="checkbox"
                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                  >
                  <label for="category-active" class="ml-2 block text-sm text-gray-900">
                    Catégorie active
                  </label>
                </div>
              </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="saving"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm"
              >
                {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
              <button
                type="button"
                @click="closeModal"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Annuler
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminCategories',
  data() {
    return {
      categories: [],
      showModal: false,
      editingCategory: {
        id: null,
        name: '',
        description: '',
        is_active: true
      },
      saving: false
    };
  },
  created() {
    this.loadCategories();
  },
  methods: {
    async loadCategories() {
      try {
        const response = await axios.get('/api/admin/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des catégories:', error);
        alert('Impossible de charger les catégories');
      }
    },
    showAddModal() {
      this.editingCategory = {
        id: null,
        name: '',
        description: '',
        is_active: true
      };
      this.showModal = true;
    },
    editCategory(category) {
      this.editingCategory = { ...category };
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.editingCategory = {
        id: null,
        name: '',
        description: '',
        is_active: true
      };
    },
    async saveCategory() {
      this.saving = true;
      try {
        if (this.editingCategory.id) {
          await axios.put(`/api/admin/categories/${this.editingCategory.id}`, this.editingCategory);
          alert('Catégorie mise à jour avec succès');
        } else {
          await axios.post('/api/admin/categories', this.editingCategory);
          alert('Catégorie créée avec succès');
        }
        
        this.closeModal();
        this.loadCategories();
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement de la catégorie:', error);
        alert('Impossible d\'enregistrer la catégorie');
      } finally {
        this.saving = false;
      }
    },
    async deleteCategory(category) {
      if (!confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${category.name}" ?`)) {
        return;
      }
      
      try {
        await axios.delete(`/api/admin/categories/${category.id}`);
        this.loadCategories();
        alert('Catégorie supprimée avec succès');
      } catch (error) {
        console.error('Erreur lors de la suppression de la catégorie:', error);
        if (error.response && error.response.status === 422) {
          alert('Impossible de supprimer cette catégorie car elle contient des produits.');
        } else {
          alert('Impossible de supprimer la catégorie');
        }
      }
    }
  }
};
</script>
EOL

cat > resources/js/routes.js << 'EOL'
import Home from './views/Home.vue';
import ProductList from './views/ProductList.vue';
import ProductDetail from './views/ProductDetail.vue';
import Login from './views/auth/Login.vue';
import Register from './views/auth/Register.vue';
import Profile from './views/auth/Profile.vue';

// Admin components
import AdminLayout from './layouts/AdminLayout.vue';
import AdminDashboard from './views/admin/AdminDashboard.vue';
import AdminProducts from './views/admin/AdminProducts.vue';
import AdminProductEdit from './views/admin/AdminProductEdit.vue';
import AdminOrders from './views/admin/AdminOrders.vue';
import AdminOrderDetails from './views/admin/AdminOrderDetails.vue';
import AdminUsers from './views/admin/AdminUsers.vue';
import AdminCategories from './views/admin/AdminCategories.vue';

// Fonction pour vérifier si l'utilisateur est authentifié
const requireAuth = (to, from, next) => {
  if (!localStorage.getItem('token')) {
    next('/login');
  } else {
    next();
  }
};

// Fonction pour vérifier si l'utilisateur est admin
const requireAdmin = (to, from, next) => {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  if (!localStorage.getItem('token') || !user.is_admin) {
    next('/');
  } else {
    next();
  }
};

// Fonction pour rediriger les utilisateurs déjà connectés
const redirectIfAuthenticated = (to, from, next) => {
  if (localStorage.getItem('token')) {
    next('/');
  } else {
    next();
  }
};

const routes = [
  { 
    path: '/', 
    component: Home, 
    name: 'home' 
  },
  { 
    path: '/products', 
    component: ProductList, 
    name: 'products' 
  },
  { 
    path: '/products/:id', 
    component: ProductDetail, 
    name: 'product-detail' 
  },
  {
    path: '/login',
    component: Login,
    name: 'login',
    beforeEnter: redirectIfAuthenticated
  },
  {
    path: '/register',
    component: Register,
    name: 'register',
    beforeEnter: redirectIfAuthenticated
  },
  {
    path: '/profile',
    component: Profile,
    name: 'profile',
    beforeEnter: requireAuth
  },
  // Routes d'administration
  {
    path: '/admin',
    component: AdminLayout,
    beforeEnter: requireAdmin,
    children: [
      {
        path: '',
        redirect: '/admin/dashboard'
      },
      {
        path: 'dashboard',
        component: AdminDashboard,
        name: 'admin-dashboard'
      },
      {
        path: 'products',
        component: AdminProducts,
        name: 'admin-products'
      },
      {
        path: 'products/new',
        component: AdminProductEdit,
        name: 'admin-product-new'
      },
      {
        path: 'products/:id/edit',
        component: AdminProductEdit,
        name: 'admin-product-edit'
      },
      {
        path: 'orders',
        component: AdminOrders,
        name: 'admin-orders'
      },
      {
        path: 'orders/:id',
        component: AdminOrderDetails,
        name: 'admin-order-details'
      },
      {
        path: 'users',
        component: AdminUsers,
        name: 'admin-users'
      },
      {
        path: 'categories',
        component: AdminCategories,
        name: 'admin-categories'
      }
    ]
  }
];

export default routes;
EOL

<template>
  <header class="bg-white shadow">
    <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
      <router-link to="/" class="text-xl font-bold text-purple-600">
        Le dressing des piplettes
      </router-link>
      
      <div class="hidden md:flex space-x-8">
        <router-link to="/" class="text-gray-700 hover:text-purple-600 transition-colors">
          Accueil
        </router-link>
        <router-link to="/products" class="text-gray-700 hover:text-purple-600 transition-colors">
          Collections
        </router-link>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          À propos
        </a>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          Contact
        </a>
      </div>
      
      <div class="flex items-center space-x-4">
        <!-- Panier -->
        <button class="text-gray-700 hover:text-purple-600 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </button>
        
        <!-- Lien Administration (si admin) -->
        <router-link 
          v-if="isLoggedIn && user && user.is_admin"
          to="/admin"
          class="text-purple-600 hover:text-purple-800 font-medium"
        >
          Administration
        </router-link>
        
        <!-- Menu utilisateur (connecté) -->
        <div v-if="isLoggedIn" class="relative">
          <button @click="userMenuOpen = !userMenuOpen" class="flex items-center text-gray-700 hover:text-purple-600 transition-colors focus:outline-none">
            <span class="mr-1">{{ user ? user.name : '' }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
          
          <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
            <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Mon profil
            </router-link>
            <router-link 
              v-if="user && user.is_admin" 
              to="/admin" 
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >
              Administration
            </router-link>
            <a href="#" @click.prevent="logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Déconnexion
            </a>
          </div>
        </div>
        
        <!-- Boutons Connexion/Inscription (non connecté) -->
        <div v-else class="flex items-center space-x-2">
          <router-link to="/login" class="text-gray-700 hover:text-purple-600 transition-colors">
            Connexion
          </router-link>
          <span class="text-gray-300">|</span>
          <router-link to="/register" class="text-gray-700 hover:text-purple-600 transition-colors">
            Inscription
          </router-link>
        </div>
        
        <!-- Bouton menu mobile -->
        <button class="md:hidden text-gray-700 hover:text-purple-600 transition-colors" @click="mobileMenuOpen = !mobileMenuOpen">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </nav>
    
    <!-- Menu mobile -->
    <div v-if="mobileMenuOpen" class="md:hidden py-2 px-4 bg-gray-50">
      <router-link to="/" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Accueil
      </router-link>
      <router-link to="/products" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Collections
      </router-link>
      <a href="#" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        À propos
      </a>
      <a href="#" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Contact
      </a>
      
      <!-- Options utilisateur mobile -->
      <div v-if="isLoggedIn" class="border-t border-gray-200 mt-2 pt-2">
        <router-link to="/profile" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Mon profil
        </router-link>
        <router-link 
          v-if="user && user.is_admin" 
          to="/admin" 
          class="block py-2 text-purple-600 hover:text-purple-800 font-medium"
        >
          Administration
        </router-link>
        <a href="#" @click.prevent="logout" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Déconnexion
        </a>
      </div>
      <div v-else class="border-t border-gray-200 mt-2 pt-2">
        <router-link to="/login" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Connexion
        </router-link>
        <router-link to="/register" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Inscription
        </router-link>
      </div>
    </div>
  </header>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AppHeader',
  data() {
    return {
      mobileMenuOpen: false,
      userMenuOpen: false,
      isLoggedIn: false,
      user: null
    };
  },
  created() {
    this.checkAuth();
    
    // Écouter l'événement de stockage pour les changements d'authentification
    window.addEventListener('storage', this.handleStorageChange);
    
    // Écouter un événement personnalisé pour les changements d'authentification
    window.addEventListener('auth-changed', this.checkAuth);
  },
  beforeUnmount() {
    // Nettoyer les écouteurs d'événements
    window.removeEventListener('storage', this.handleStorageChange);
    window.removeEventListener('auth-changed', this.checkAuth);
  },
  methods: {
    checkAuth() {
      const token = localStorage.getItem('token');
      const userJson = localStorage.getItem('user');
      
      this.isLoggedIn = !!token;
      this.user = userJson ? JSON.parse(userJson) : null;
      
      // Configurer axios avec le token
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      } else {
        delete axios.defaults.headers.common['Authorization'];
      }
    },
    
    handleStorageChange(event) {
      // Réagir aux changements de stockage (utile pour la synchronisation entre onglets)
      if (event.key === 'token' || event.key === 'user') {
        this.checkAuth();
      }
    },
    
    async logout() {
      try {
        // Fermer les menus
        this.userMenuOpen = false;
        this.mobileMenuOpen = false;
        
        // Appeler l'API de déconnexion
        await axios.post('/api/logout');
        
        // Supprimer les données d'authentification
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        
        // Mettre à jour l'état d'authentification
        this.isLoggedIn = false;
        this.user = null;
        
        // Supprimer le token des en-têtes axios
        delete axios.defaults.headers.common['Authorization'];
        
        // Émettre l'événement auth-changed
        window.dispatchEvent(new CustomEvent('auth-changed'));
        
        // Rediriger vers la page d'accueil
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur lors de la déconnexion:', error);
        
        // En cas d'erreur, déconnecter quand même localement
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        this.isLoggedIn = false;
        this.user = null;
        delete axios.defaults.headers.common['Authorization'];
        window.dispatchEvent(new CustomEvent('auth-changed'));
        this.$router.push('/');
      }
    }
  }
};
</script>

cat > resources/js/components/AppHeader.vue << 'EOL'
<template>
  <header class="bg-white shadow">
    <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
      <router-link to="/" class="text-xl font-bold text-purple-600">
        Le dressing des piplettes
      </router-link>
      
      <div class="hidden md:flex space-x-8">
        <router-link to="/" class="text-gray-700 hover:text-purple-600 transition-colors">
          Accueil
        </router-link>
        <router-link to="/products" class="text-gray-700 hover:text-purple-600 transition-colors">
          Collections
        </router-link>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          À propos
        </a>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          Contact
        </a>
      </div>
      
      <div class="flex items-center space-x-4">
        <!-- Panier -->
        <button class="text-gray-700 hover:text-purple-600 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </button>
        
        <!-- Lien Administration (si admin) -->
        <router-link 
          v-if="isLoggedIn && user && user.is_admin"
          to="/admin"
          class="text-purple-600 hover:text-purple-800 font-medium"
        >
          Administration
        </router-link>
        
        <!-- Menu utilisateur (connecté) -->
        <div v-if="isLoggedIn" class="relative">
          <button @click="userMenuOpen = !userMenuOpen" class="flex items-center text-gray-700 hover:text-purple-600 transition-colors focus:outline-none">
            <span class="mr-1">{{ user ? user.name : '' }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
          
          <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
            <router-link to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Mon profil
            </router-link>
            <router-link 
              v-if="user && user.is_admin" 
              to="/admin" 
              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
            >
              Administration
            </router-link>
            <a href="#" @click.prevent="logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              Déconnexion
            </a>
          </div>
        </div>
        
        <!-- Boutons Connexion/Inscription (non connecté) -->
        <div v-else class="flex items-center space-x-2">
          <router-link to="/login" class="text-gray-700 hover:text-purple-600 transition-colors">
            Connexion
          </router-link>
          <span class="text-gray-300">|</span>
          <router-link to="/register" class="text-gray-700 hover:text-purple-600 transition-colors">
            Inscription
          </router-link>
        </div>
        
        <!-- Bouton menu mobile -->
        <button class="md:hidden text-gray-700 hover:text-purple-600 transition-colors" @click="mobileMenuOpen = !mobileMenuOpen">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </nav>
    
    <!-- Menu mobile -->
    <div v-if="mobileMenuOpen" class="md:hidden py-2 px-4 bg-gray-50">
      <router-link to="/" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Accueil
      </router-link>
      <router-link to="/products" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Collections
      </router-link>
      <a href="#" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        À propos
      </a>
      <a href="#" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
        Contact
      </a>
      
      <!-- Options utilisateur mobile -->
      <div v-if="isLoggedIn" class="border-t border-gray-200 mt-2 pt-2">
        <router-link to="/profile" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Mon profil
        </router-link>
        <router-link 
          v-if="user && user.is_admin" 
          to="/admin" 
          class="block py-2 text-purple-600 hover:text-purple-800 font-medium"
        >
          Administration
        </router-link>
        <a href="#" @click.prevent="logout" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Déconnexion
        </a>
      </div>
      <div v-else class="border-t border-gray-200 mt-2 pt-2">
        <router-link to="/login" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Connexion
        </router-link>
        <router-link to="/register" class="block py-2 text-gray-700 hover:text-purple-600 transition-colors">
          Inscription
        </router-link>
      </div>
    </div>
  </header>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AppHeader',
  data() {
    return {
      mobileMenuOpen: false,
      userMenuOpen: false,
      isLoggedIn: false,
      user: null
    };
  },
  created() {
    this.checkAuth();
    
    // Écouter l'événement de stockage pour les changements d'authentification
    window.addEventListener('storage', this.handleStorageChange);
    
    // Écouter un événement personnalisé pour les changements d'authentification
    window.addEventListener('auth-changed', this.checkAuth);
  },
  beforeUnmount() {
    // Nettoyer les écouteurs d'événements
    window.removeEventListener('storage', this.handleStorageChange);
    window.removeEventListener('auth-changed', this.checkAuth);
  },
  methods: {
    checkAuth() {
      const token = localStorage.getItem('token');
      const userJson = localStorage.getItem('user');
      
      this.isLoggedIn = !!token;
      this.user = userJson ? JSON.parse(userJson) : null;
      
      // Configurer axios avec le token
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      } else {
        delete axios.defaults.headers.common['Authorization'];
      }
    },
    
    handleStorageChange(event) {
      // Réagir aux changements de stockage (utile pour la synchronisation entre onglets)
      if (event.key === 'token' || event.key === 'user') {
        this.checkAuth();
      }
    },
    
    async logout() {
      try {
        // Fermer les menus
        this.userMenuOpen = false;
        this.mobileMenuOpen = false;
        
        // Appeler l'API de déconnexion
        await axios.post('/api/logout');
        
        // Supprimer les données d'authentification
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        
        // Mettre à jour l'état d'authentification
        this.isLoggedIn = false;
        this.user = null;
        
        // Supprimer le token des en-têtes axios
        delete axios.defaults.headers.common['Authorization'];
        
        // Émettre l'événement auth-changed
        window.dispatchEvent(new CustomEvent('auth-changed'));
        
        // Rediriger vers la page d'accueil
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur lors de la déconnexion:', error);
        
        // En cas d'erreur, déconnecter quand même localement
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        this.isLoggedIn = false;
        this.user = null;
        delete axios.defaults.headers.common['Authorization'];
        window.dispatchEvent(new CustomEvent('auth-changed'));
        this.$router.push('/');
      }
    }
  }
};
</script>
EOL

cat > routes/web.php << 'EOL'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategoryController;

// Routes API avec préfixe explicite
Route::prefix('api')->group(function () {
    // Routes d'authentification
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    
    // Routes protégées par authentification
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/user', [AuthController::class, 'updateProfile']);
        
        // Routes pour les commandes de l'utilisateur connecté
        Route::get('/user/orders', [OrderController::class, 'index']);
        
        // Routes d'administration protégées par le rôle admin
        Route::middleware('role:admin')->group(function () {
            // Dashboard
            Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
            
            // Gestion des utilisateurs
            Route::get('/admin/users', [UserController::class, 'index']);
            Route::get('/admin/users/{user}', [UserController::class, 'show']);
            Route::put('/admin/users/{user}', [UserController::class, 'update']);
            Route::delete('/admin/users/{user}', [UserController::class, 'destroy']);
            
            // Gestion des produits
            Route::get('/admin/products', [ProductController::class, 'adminIndex']);
            Route::post('/admin/products', [ProductController::class, 'store']);
            Route::get('/admin/products/{product}', [ProductController::class, 'show']);
            Route::put('/admin/products/{product}', [ProductController::class, 'update']);
            Route::delete('/admin/products/{product}', [ProductController::class, 'destroy']);
            
            // Gestion des commandes
            Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
            Route::get('/admin/orders/{order}', [OrderController::class, 'adminShow']);
            Route::put('/admin/orders/{order}', [OrderController::class, 'adminUpdate']);
            
            // Gestion des catégories
            Route::get('/admin/categories', [CategoryController::class, 'index']);
            Route::post('/admin/categories', [CategoryController::class, 'store']);
            Route::put('/admin/categories/{category}', [CategoryController::class, 'update']);
            Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy']);
        });
    });
    
    // Routes pour les produits
    Route::apiResource('products', ProductController::class);
    
    // Routes pour les commandes
    Route::apiResource('orders', OrderController::class);
    
    // Route spécifique pour créer une commande depuis le live
    Route::post('orders/live', [OrderController::class, 'createLiveOrder']);
    
    // Route pour obtenir les produits disponibles en live
    Route::get('products/live', [ProductController::class, 'getLiveProducts']);
    
    // Route pour mettre à jour le stock d'un produit
    Route::patch('products/{product}/stock', [ProductController::class, 'updateStock']);
});

// Routes Web pour servir l'application Vue.js
Route::get('/', function () {
    return view('app');
});

// Catch-all route for SPA
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
EOL

cat > bootstrap/app.php << 'EOL'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
        ]);
        
        // Remplacer le middleware de vérification CSRF par défaut par notre version personnalisée
        $middleware->replace(
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\VerifyCsrfToken::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
EOL

cat > app/Http/Middleware/CheckUserRole.php << 'EOL'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }
        
        if ($role === 'admin' && !$request->user()->is_admin) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        
        return $next($request);
    }
}
EOL

cat > app/Http/Controllers/API/CategoryController.php << 'EOL'
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
EOL

cat > app/Models/Category.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
EOL

php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear

php artisan migrate:fresh --seed

cat > resources/js/views/admin/AdminCategories.vue << 'EOL'
<template>
  <div class="admin-categories">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-800">Gestion des catégories</h2>
      <button
        @click="showAddModal"
        class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
      >
        Ajouter une catégorie
      </button>
    </div>
    
    <!-- Liste des catégories -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Nom
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Description
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Nombre de produits
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Statut
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="category in categories" :key="category.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-500">{{ category.description || '-' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ category.products_count || 0 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ category.is_active ? 'Actif' : 'Inactif' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="editCategory(category)"
                class="text-indigo-600 hover:text-indigo-900 mr-2"
              >
                Modifier
              </button>
              <button
                @click="deleteCategory(category)"
                class="text-red-600 hover:text-red-900"
              >
                Supprimer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Modal d'ajout/édition -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>
        
        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="saveCategory">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                {{ editingCategory.id ? 'Modifier la catégorie' : 'Ajouter une catégorie' }}
              </h3>
              
              <div class="space-y-4">
                <div>
                  <label for="category-name" class="block text-sm font-medium text-gray-700">Nom</label>
                  <input
                    id="category-name"
                    v-model="editingCategory.name"
                    type="text"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                  >
                </div>
                
                <div>
                  <label for="category-description" class="block text-sm font-medium text-gray-700">Description</label>
                  <textarea
                    id="category-description"
                    v-model="editingCategory.description"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                  ></textarea>
                </div>
                
                <div class="flex items-center">
                  <input
                    id="category-active"
                    v-model="editingCategory.is_active"
                    type="checkbox"
                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                  >
                  <label for="category-active" class="ml-2 block text-sm text-gray-900">
                    Catégorie active
                  </label>
                </div>
              </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="saving"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
              <button
                type="button"
                @click="closeModal"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Annuler
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminCategories',
  data() {
    return {
      categories: [],
      showModal: false,
      editingCategory: {
        id: null,
        name: '',
        description: '',
        is_active: true
      },
      saving: false
    };
  },
  created() {
    this.loadCategories();
  },
  methods: {
    async loadCategories() {
      try {
        const response = await axios.get('/api/admin/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des catégories:', error);
        alert('Impossible de charger les catégories');
      }
    },
    
    showAddModal() {
      this.editingCategory = {
        id: null,
        name: '',
        description: '',
        is_active: true
      };
      this.showModal = true;
    },
    
    editCategory(category) {
      this.editingCategory = { ...category };
      this.showModal = true;
    },
    
    closeModal() {
      this.showModal = false;
      this.editingCategory = {
        id: null,
        name: '',
        description: '',
        is_active: true
      };
    },
    
    async saveCategory() {
      this.saving = true;
      try {
        if (this.editingCategory.id) {
          await axios.put(`/api/admin/categories/${this.editingCategory.id}`, this.editingCategory);
          alert('Catégorie mise à jour avec succès');
        } else {
          await axios.post('/api/admin/categories', this.editingCategory);
          alert('Catégorie créée avec succès');
        }
        
        this.closeModal();
        this.loadCategories();
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement de la catégorie:', error);
        alert('Impossible d\'enregistrer la catégorie');
      } finally {
        this.saving = false;
      }
    },
    
    async deleteCategory(category) {
      if (!confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${category.name}" ?`)) {
        return;
      }
      
      try {
        await axios.delete(`/api/admin/categories/${category.id}`);
        this.loadCategories();
        alert('Catégorie supprimée avec succès');
      } catch (error) {
        console.error('Erreur lors de la suppression de la catégorie:', error);
        if (error.response && error.response.status === 422) {
          alert('Impossible de supprimer cette catégorie car elle contient des produits.');
        } else {
          alert('Impossible de supprimer la catégorie');
        }
      }
    }
  }
};
</script>

<style scoped>
/* Ajout de styles supplémentaires si nécessaire */
input[type="text"],
input[type="number"],
textarea,
select {
  @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm;
}
</style>
EOL



cat > resources/js/views/admin/AdminCategories.vue << 'EOL'
<template>
  <div class="admin-categories">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-800">Gestion des catégories</h2>
      <button
        @click="showAddModal"
        class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
      >
        Ajouter une catégorie
      </button>
    </div>
    
    <!-- Liste des catégories -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Nom
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Description
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Nombre de produits
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Statut
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="category in categories" :key="category.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-500">{{ category.description || '-' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ category.products_count || 0 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ category.is_active ? 'Actif' : 'Inactif' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="editCategory(category)"
                class="text-indigo-600 hover:text-indigo-900 mr-2"
              >
                Modifier
              </button>
              <button
                @click="deleteCategory(category)"
                class="text-red-600 hover:text-red-900"
              >
                Supprimer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Modal d'ajout/édition -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal-container">
        <div class="modal-content">
          <form @submit.prevent="saveCategory">
            <div class="modal-header">
              <h3 class="modal-title">
                {{ editingCategory.id ? 'Modifier la catégorie' : 'Ajouter une catégorie' }}
              </h3>
            </div>
            
            <div class="modal-body">
              <div class="form-group">
                <label for="category-name" class="form-label">Nom</label>
                <input
                  id="category-name"
                  v-model="editingCategory.name"
                  type="text"
                  required
                  class="form-input"
                >
              </div>
              
              <div class="form-group">
                <label for="category-description" class="form-label">Description</label>
                <textarea
                  id="category-description"
                  v-model="editingCategory.description"
                  rows="3"
                  class="form-input"
                ></textarea>
              </div>
              
              <div class="form-group checkbox-group">
                <input
                  id="category-active"
                  v-model="editingCategory.is_active"
                  type="checkbox"
                  class="form-checkbox"
                >
                <label for="category-active" class="checkbox-label">
                  Catégorie active
                </label>
              </div>
            </div>
            
            <div class="modal-footer">
              <button
                type="button"
                @click="closeModal"
                class="btn btn-secondary"
              >
                Annuler
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="btn btn-primary"
              >
                {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminCategories',
  data() {
    return {
      categories: [],
      showModal: false,
      editingCategory: {
        id: null,
        name: '',
        description: '',
        is_active: true
      },
      saving: false
    };
  },
  created() {
    this.loadCategories();
  },
  methods: {
    async loadCategories() {
      try {
        const response = await axios.get('/api/admin/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des catégories:', error);
        alert('Impossible de charger les catégories');
      }
    },
    
    showAddModal() {
      this.editingCategory = {
        id: null,
        name: '',
        description: '',
        is_active: true
      };
      this.showModal = true;
    },
    
    editCategory(category) {
      this.editingCategory = { ...category };
      this.showModal = true;
    },
    
    closeModal() {
      this.showModal = false;
      this.editingCategory = {
        id: null,
        name: '',
        description: '',
        is_active: true
      };
    },
    
    async saveCategory() {
      this.saving = true;
      try {
        if (this.editingCategory.id) {
          await axios.put(`/api/admin/categories/${this.editingCategory.id}`, this.editingCategory);
          alert('Catégorie mise à jour avec succès');
        } else {
          await axios.post('/api/admin/categories', this.editingCategory);
          alert('Catégorie créée avec succès');
        }
        
        this.closeModal();
        this.loadCategories();
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement de la catégorie:', error);
        alert('Impossible d\'enregistrer la catégorie');
      } finally {
        this.saving = false;
      }
    },
    
    async deleteCategory(category) {
      if (!confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${category.name}" ?`)) {
        return;
      }
      
      try {
        await axios.delete(`/api/admin/categories/${category.id}`);
        this.loadCategories();
        alert('Catégorie supprimée avec succès');
      } catch (error) {
        console.error('Erreur lors de la suppression de la catégorie:', error);
        if (error.response && error.response.status === 422) {
          alert('Impossible de supprimer cette catégorie car elle contient des produits.');
        } else {
          alert('Impossible de supprimer la catégorie');
        }
      }
    }
  }
};
</script>

<style scoped>
/* Modal Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-container {
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.modal-header {
  padding: 20px;
  border-bottom: 1px solid #e5e5e5;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.modal-body {
  padding: 20px;
}

.modal-footer {
  padding: 20px;
  border-top: 1px solid #e5e5e5;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

/* Form Styles */
.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.form-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
  transition: border-color 0.15s;
}

.form-input:focus {
  outline: none;
  border-color: #7c3aed;
  box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.checkbox-group {
  display: flex;
  align-items: center;
}

.form-checkbox {
  width: 16px;
  height: 16px;
  margin-right: 8px;
  cursor: pointer;
}

.checkbox-label {
  cursor: pointer;
  font-size: 0.875rem;
  color: #374151;
}

/* Button Styles */
.btn {
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s;
  border: none;
}

.btn-primary {
  background-color: #7c3aed;
  color: white;
}

.btn-primary:hover {
  background-color: #6d28d9;
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: white;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background-color: #f9fafb;
}
</style>
EOL

cat > app/Http/Controllers/API/UserController.php << 'EOL'
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filtrage par recherche
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par rôle
        if ($request->has('is_admin') && $request->is_admin != '') {
            $query->where('is_admin', $request->is_admin === 'true');
        }
        
        $users = $query->withCount('orders')->paginate(10);
        
        return response()->json($users);
    }
    
    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['orders.items.product']);
        return response()->json($user);
    }
    
    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'is_admin' => 'boolean',
            'email_verified' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $userData = $request->only(['name', 'email', 'is_admin', 'email_verified']);
        
        if ($request->has('password') && $request->password) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $user->update($userData);
        
        return response()->json($user);
    }
    
    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Ne pas supprimer le compte admin principal
        if ($user->email === 'admin@dressingdespiplettes.com') {
            return response()->json(['message' => 'Impossible de supprimer le compte administrateur principal.'], 403);
        }
        
        $user->delete();
        
        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }
}
EOL

php artisan make:seeder UserSeeder

cat > database/seeders/UserSeeder.php << 'EOL'
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques utilisateurs de test
        User::create([
            'name' => 'Marie Dupont',
            'email' => 'marie.dupont@example.com',
            'password' => Hash::make('password123'),
            'email_verified' => true,
            'is_admin' => false,
        ]);
        
        User::create([
            'name' => 'Jean Martin',
            'email' => 'jean.martin@example.com',
            'password' => Hash::make('password123'),
            'email_verified' => true,
            'is_admin' => false,
        ]);
        
        User::create([
            'name' => 'Sophie Bernard',
            'email' => 'sophie.bernard@example.com',
            'password' => Hash::make('password123'),
            'email_verified' => false,
            'is_admin' => false,
        ]);
        
        User::create([
            'name' => 'Pierre Dubois',
            'email' => 'pierre.dubois@example.com',
            'password' => Hash::make('password123'),
            'email_verified' => true,
            'is_admin' => true,
        ]);
    }
}
EOL

cat > database/seeders/DatabaseSeeder.php << 'EOL'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SizeSeeder::class,
            ProductSeeder::class,
            AdminUserSeeder::class,
            UserSeeder::class,
        ]);
    }
}
EOL

php artisan db:seed --class=UserSeeder

cat > resources/js/views/admin/AdminUsers.vue << 'EOL'
<template>
  <div class="admin-users">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-800">Gestion des utilisateurs</h2>
      <button
        @click="showAddModal"
        class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
      >
        Ajouter un utilisateur
      </button>
    </div>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Nom, email..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @input="debounceSearch"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
          <select
            v-model="filters.is_admin"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadUsers"
          >
            <option value="">Tous les utilisateurs</option>
            <option value="true">Administrateurs</option>
            <option value="false">Clients</option>
          </select>
        </div>
      </div>
    </div>
    
    <!-- Message de chargement -->
    <div v-if="loading" class="text-center py-8">
      <p>Chargement des utilisateurs...</p>
    </div>
    
    <!-- Message d'erreur -->
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <p>{{ error }}</p>
    </div>
    
    <!-- Message si aucun utilisateur -->
    <div v-else-if="users.length === 0" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
      <p>Aucun utilisateur trouvé.</p>
    </div>
    
    <!-- Liste des utilisateurs -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Utilisateur
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Type
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Email vérifié
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Date d'inscription
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Commandes
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="user.is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'"
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ user.is_admin ? 'Admin' : 'Client' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="user.email_verified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ user.email_verified ? 'Vérifié' : 'Non vérifié' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ user.orders_count || 0 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="editUser(user)"
                class="text-indigo-600 hover:text-indigo-900 mr-2"
              >
                Modifier
              </button>
              <button
                v-if="user.email !== 'admin@dressingdespiplettes.com'"
                @click="deleteUser(user)"
                class="text-red-600 hover:text-red-900"
              >
                Supprimer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Affichage de
              <span class="font-medium">{{ pagination.from }}</span>
              à
              <span class="font-medium">{{ pagination.to }}</span>
              sur
              <span class="font-medium">{{ pagination.total }}</span>
              résultats
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                Précédent
              </button>
              <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                Page {{ pagination.current_page }} sur {{ pagination.last_page }}
              </span>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                Suivant
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal d'ajout/édition -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal-container">
        <div class="modal-content">
          <form @submit.prevent="saveUser">
            <div class="modal-header">
              <h3 class="modal-title">
                {{ editingUser.id ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur' }}
              </h3>
            </div>
            
            <div class="modal-body">
              <div class="form-group">
                <label for="user-name" class="form-label">Nom</label>
                <input
                  id="user-name"
                  v-model="editingUser.name"
                  type="text"
                  required
                  class="form-input"
                >
              </div>
              
              <div class="form-group">
                <label for="user-email" class="form-label">Email</label>
                <input
                  id="user-email"
                  v-model="editingUser.email"
                  type="email"
                  required
                  class="form-input"
                >
              </div>
              
              <div class="form-group">
                <label for="user-password" class="form-label">
                  {{ editingUser.id ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe' }}
                </label>
                <input
                  id="user-password"
                  v-model="editingUser.password"
                  type="password"
                  :required="!editingUser.id"
                  class="form-input"
                >
              </div>
              
              <div class="form-group checkbox-group">
                <input
                  id="user-is-admin"
                  v-model="editingUser.is_admin"
                  type="checkbox"
                  class="form-checkbox"
                >
                <label for="user-is-admin" class="checkbox-label">
                  Administrateur (peut accéder au panneau d'administration)
                </label>
              </div>
              
              <div class="form-group checkbox-group">
                <input
                  id="user-email-verified"
                  v-model="editingUser.email_verified"
                  type="checkbox"
                  class="form-checkbox"
                >
                <label for="user-email-verified" class="checkbox-label">
                  Email vérifié
                </label>
              </div>
            </div>
            
            <div class="modal-footer">
              <button
                type="button"
                @click="closeModal"
                class="btn btn-secondary"
              >
                Annuler
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="btn btn-primary"
              >
                {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminUsers',
  data() {
    return {
      users: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      },
      filters: {
        search: '',
        is_admin: ''
      },
      loading: false,
      error: null,
      showModal: false,
      editingUser: {
        id: null,
        name: '',
        email: '',
        password: '',
        is_admin: false,
        email_verified: true
      },
      saving: false,
      searchTimeout: null
    };
  },
  created() {
    this.loadUsers();
  },
  methods: {
    async loadUsers(page = 1) {
      this.loading = true;
      this.error = null;
      
      try {
        const params = {
          page,
          ...this.filters
        };
        
        const response = await axios.get('/api/admin/users', { params });
        
        this.users = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page,
          total: response.data.total,
          from: response.data.from || 0,
          to: response.data.to || 0
        };
      } catch (error) {
        console.error('Erreur lors du chargement des utilisateurs:', error);
        this.error = error.response?.data?.message || 'Impossible de charger les utilisateurs';
      } finally {
        this.loading = false;
      }
    },
    
    debounceSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.loadUsers(1);
      }, 500);
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.loadUsers(page);
      }
    },
    
    formatDate(dateString) {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      }).format(new Date(dateString));
    },
    
    showAddModal() {
      this.editingUser = {
        id: null,
        name: '',
        email: '',
        password: '',
        is_admin: false,
        email_verified: true
      };
      this.showModal = true;
    },
    
    editUser(user) {
      this.editingUser = {
        ...user,
        password: ''
      };
      this.showModal = true;
    },
    
    closeModal() {
      this.showModal = false;
      this.editingUser = {
        id: null,
        name: '',
        email: '',
        password: '',
        is_admin: false,
        email_verified: true
      };
    },
    
    async saveUser() {
      this.saving = true;
      
      try {
        const data = {
          name: this.editingUser.name,
          email: this.editingUser.email,
          is_admin: this.editingUser.is_admin,
          email_verified: this.editingUser.email_verified
        };
        
        if (this.editingUser.password) {
          data.password = this.editingUser.password;
        }
        
        if (this.editingUser.id) {
          // Mise à jour
          await axios.put(`/api/admin/users/${this.editingUser.id}`, data);
          alert('Utilisateur mis à jour avec succès');
        } else {
          // Création
          await axios.post('/api/admin/users', data);
          alert('Utilisateur créé avec succès');
        }
        
        this.closeModal();
        this.loadUsers(this.pagination.current_page);
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement de l\'utilisateur:', error);
        alert('Impossible d\'enregistrer l\'utilisateur');
      } finally {
        this.saving = false;
      }
    },
    
    async deleteUser(user) {
      if (!confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${user.name}" ?`)) {
        return;
      }
      
      try {
        await axios.delete(`/api/admin/users/${user.id}`);
        this.loadUsers(this.pagination.current_page);
        alert('Utilisateur supprimé avec succès');
      } catch (error) {
        console.error('Erreur lors de la suppression de l\'utilisateur:', error);
        if (error.response && error.response.status === 403) {
          alert('Impossible de supprimer cet utilisateur. Il s\'agit peut-être du compte administrateur principal.');
        } else {
          alert('Impossible de supprimer l\'utilisateur');
        }
      }
    }
  }
};
</script>

<style scoped>
/* Modal Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-container {
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.modal-header {
  padding: 20px;
  border-bottom: 1px solid #e5e5e5;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.modal-body {
  padding: 20px;
}

.modal-footer {
  padding: 20px;
  border-top: 1px solid #e5e5e5;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

/* Form Styles */
.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
  color: #374151;
  font-size: 0.875rem;
}

.form-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
  transition: border-color 0.15s;
}

.form-input:focus {
  outline: none;
  border-color: #7c3aed;
  box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.checkbox-group {
  display: flex;
  align-items: center;
}

.form-checkbox {
  width: 16px;
  height: 16px;
  margin-right: 8px;
  cursor: pointer;
}

.checkbox-label {
  cursor: pointer;
  font-size: 0.875rem;
  color: #374151;
}

/* Button Styles */
.btn {
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s;
  border: none;
}

.btn-primary {
  background-color: #7c3aed;
  color: white;
}

.btn-primary:hover {
  background-color: #6d28d9;
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: white;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background-color: #f9fafb;
}
</style>
EOL

cat > app/Http/Controllers/API/UserController.php << 'EOL'
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filtrage par recherche
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par rôle
        if ($request->has('is_admin') && $request->is_admin != '') {
            $query->where('is_admin', $request->is_admin === 'true');
        }
        
        $users = $query->withCount('orders')->paginate(10);
        
        return response()->json($users);
    }
    
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'boolean',
            'email_verified' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->is_admin ?? false,
            'email_verified' => $request->email_verified ?? true,
        ]);
        
        return response()->json($user, 201);
    }
    
    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['orders.items.product']);
        return response()->json($user);
    }
    
    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'is_admin' => 'boolean',
            'email_verified' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $userData = $request->only(['name', 'email', 'is_admin', 'email_verified']);
        
        if ($request->has('password') && $request->password) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $user->update($userData);
        
        return response()->json($user);
    }
    
    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Ne pas supprimer le compte admin principal
        if ($user->email === 'admin@dressingdespiplettes.com') {
            return response()->json(['message' => 'Impossible de supprimer le compte administrateur principal.'], 403);
        }
        
        $user->delete();
        
        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }
}
EOL

cat > routes/web.php << 'EOL'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategoryController;

// Routes API avec préfixe explicite
Route::prefix('api')->group(function () {
    // Routes d'authentification
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    
    // Routes protégées par authentification
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/user', [AuthController::class, 'updateProfile']);
        
        // Routes pour les commandes de l'utilisateur connecté
        Route::get('/user/orders', [OrderController::class, 'index']);
        
        // Routes d'administration protégées par le rôle admin
        Route::middleware('role:admin')->group(function () {
            // Dashboard
            Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
            
            // Gestion des utilisateurs
            Route::get('/admin/users', [UserController::class, 'index']);
            Route::post('/admin/users', [UserController::class, 'store']); // Ajout de la route de création
            Route::get('/admin/users/{user}', [UserController::class, 'show']);
            Route::put('/admin/users/{user}', [UserController::class, 'update']);
            Route::delete('/admin/users/{user}', [UserController::class, 'destroy']);
            
            // Gestion des produits
            Route::get('/admin/products', [ProductController::class, 'adminIndex']);
            Route::post('/admin/products', [ProductController::class, 'store']);
            Route::get('/admin/products/{product}', [ProductController::class, 'show']);
            Route::put('/admin/products/{product}', [ProductController::class, 'update']);
            Route::delete('/admin/products/{product}', [ProductController::class, 'destroy']);
            
            // Gestion des commandes
            Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
            Route::get('/admin/orders/{order}', [OrderController::class, 'adminShow']);
            Route::put('/admin/orders/{order}', [OrderController::class, 'adminUpdate']);
            
            // Gestion des catégories
            Route::get('/admin/categories', [CategoryController::class, 'index']);
            Route::post('/admin/categories', [CategoryController::class, 'store']);
            Route::put('/admin/categories/{category}', [CategoryController::class, 'update']);
            Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy']);
        });
    });
    
    // Routes pour les produits
    Route::apiResource('products', ProductController::class);
    
    // Routes pour les commandes
    Route::apiResource('orders', OrderController::class);
    
    // Route spécifique pour créer une commande depuis le live
    Route::post('orders/live', [OrderController::class, 'createLiveOrder']);
    
    // Route pour obtenir les produits disponibles en live
    Route::get('products/live', [ProductController::class, 'getLiveProducts']);
    
    // Route pour mettre à jour le stock d'un produit
    Route::patch('products/{product}/stock', [ProductController::class, 'updateStock']);
});

// Routes Web pour servir l'application Vue.js
Route::get('/', function () {
    return view('app');
});

// Catch-all route for SPA
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
EOL

cat > app/Http/Controllers/API/ProductController.php << 'EOL'
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
EOL

php artisan tinker
>>> \App\Models\Product::count()
>>> exit

http://localhost:8000/api/admin/products

cat > resources/js/views/admin/AdminProducts.vue << 'EOL'
<template>
  <div class="admin-products">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-800">Gestion des produits</h2>
      <router-link
        to="/admin/products/new"
        class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
      >
        Ajouter un produit
      </router-link>
    </div>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Nom, référence..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @input="debounceSearch"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
          <select
            v-model="filters.category_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadProducts"
          >
            <option value="">Toutes les catégories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
          <select
            v-model="filters.is_active"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadProducts"
          >
            <option value="">Tous les statuts</option>
            <option value="true">Actif</option>
            <option value="false">Inactif</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
          <select
            v-model="filters.stock_status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadProducts"
          >
            <option value="">Tous</option>
            <option value="low">Stock faible</option>
            <option value="out">Rupture de stock</option>
          </select>
        </div>
      </div>
    </div>
    
    <!-- Message de chargement -->
    <div v-if="loading" class="text-center py-8">
      <p>Chargement des produits...</p>
    </div>
    
    <!-- Message d'erreur -->
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <p>{{ error }}</p>
    </div>
    
    <!-- Message si aucun produit -->
    <div v-else-if="products.length === 0" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
      <p>Aucun produit trouvé.</p>
    </div>
    
    <!-- Liste des produits -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Produit
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Référence
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Catégorie
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Prix
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Stock
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Statut
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in products" :key="product.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="h-10 w-10 flex-shrink-0">
                  <img class="h-10 w-10 rounded-full object-cover" :src="product.image_url" :alt="product.name">
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ product.reference }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ product.category ? product.category.name : '-' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatCurrency(product.price) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              <div v-if="product.sizes && product.sizes.length > 0">
                <span v-for="size in product.sizes" :key="size.id" class="inline-block mr-2">
                  {{ size.name }}: {{ size.pivot.stock }}
                </span>
              </div>
              <span v-else>-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ product.is_active ? 'Actif' : 'Inactif' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <router-link
                :to="`/admin/products/${product.id}/edit`"
                class="text-indigo-600 hover:text-indigo-900 mr-2"
              >
                Modifier
              </router-link>
              <button
                @click="deleteProduct(product)"
                class="text-red-600 hover:text-red-900"
              >
                Supprimer
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Précédent
          </button>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Suivant
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Affichage de
              <span class="font-medium">{{ pagination.from }}</span>
              à
              <span class="font-medium">{{ pagination.to }}</span>
              sur
              <span class="font-medium">{{ pagination.total }}</span>
              résultats
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                Précédent
              </button>
              <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                Page {{ pagination.current_page }} sur {{ pagination.last_page }}
              </span>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                Suivant
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminProducts',
  data() {
    return {
      products: [],
      categories: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      },
      filters: {
        search: '',
        category_id: '',
        is_active: '',
        stock_status: ''
      },
      loading: false,
      error: null,
      searchTimeout: null
    };
  },
  created() {
    this.loadCategories();
    this.loadProducts();
  },
  methods: {
    async loadCategories() {
      try {
        const response = await axios.get('/api/admin/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des catégories:', error);
      }
    },
    
    async loadProducts(page = 1) {
      this.loading = true;
      this.error = null;
      
      try {
        const params = {
          page,
          ...this.filters
        };
        
        console.log('Loading products with params:', params);
        
        const response = await axios.get('/api/admin/products', { params });
        
        console.log('Products response:', response.data);
        
        this.products = response.data.data || [];
        this.pagination = {
          current_page: response.data.current_page || 1,
          last_page: response.data.last_page || 1,
          per_page: response.data.per_page || 10,
          total: response.data.total || 0,
          from: response.data.from || 0,
          to: response.data.to || 0
        };
      } catch (error) {
        console.error('Erreur lors du chargement des produits:', error);
        this.error = error.response?.data?.message || 'Impossible de charger les produits';
      } finally {
        this.loading = false;
      }
    },
    
    debounceSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.loadProducts(1);
      }, 500);
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.loadProducts(page);
      }
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount);
    },
    
    async deleteProduct(product) {
      if (!confirm(`Êtes-vous sûr de vouloir supprimer le produit "${product.name}" ?`)) {
        return;
      }
      
      try {
        await axios.delete(`/api/admin/products/${product.id}`);
        this.loadProducts(this.pagination.current_page);
        alert('Produit supprimé avec succès');
      } catch (error) {
        console.error('Erreur lors de la suppression du produit:', error);
        alert('Impossible de supprimer le produit');
      }
    }
  }
};
</script>
EOL

cat > app/Http/Controllers/API/AdminController.php << 'EOL'
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function dashboard(Request $request)
    {
        try {
            // Statistiques générales
            $stats = [
                'total_users' => User::count(),
                'total_products' => Product::count(),
                'total_orders' => Order::count(),
                'total_revenue' => Order::where('status', 'paid')->sum('total_amount'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'recent_orders' => Order::with(['user', 'items.product'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
                'low_stock_products' => DB::table('product_size')
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
                    ->get(),
                'monthly_revenue' => $this->getMonthlyRevenue(),
                'orders_by_status' => $this->getOrdersByStatus(),
                'new_users_today' => User::whereDate('created_at', Carbon::today())->count(),
                'orders_today' => Order::whereDate('created_at', Carbon::today())->count(),
            ];
            
            return response()->json($stats);
        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement des statistiques'], 500);
        }
    }
    
    /**
     * Get monthly revenue for the current year
     */
    private function getMonthlyRevenue()
    {
        $currentYear = Carbon::now()->year;
        
        $monthlyRevenue = Order::selectRaw('
                MONTH(created_at) as month,
                YEAR(created_at) as year,
                SUM(total_amount) as total
            ')
            ->where('status', 'paid')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month', 'year')
            ->orderBy('month')
            ->get();
            
        // Créer un tableau avec tous les mois de l'année
        $allMonths = [];
        for ($i = 1; $i <= 12; $i++) {
            $found = false;
            foreach ($monthlyRevenue as $revenue) {
                if ($revenue->month == $i) {
                    $allMonths[] = [
                        'month' => $i,
                        'year' => $currentYear,
                        'total' => $revenue->total
                    ];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $allMonths[] = [
                    'month' => $i,
                    'year' => $currentYear,
                    'total' => 0
                ];
            }
        }
        
        return $allMonths;
    }
    
    /**
     * Get orders count by status
     */
    private function getOrdersByStatus()
    {
        return Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }
}
EOL

cat > resources/js/views/admin/AdminDashboard.vue << 'EOL'
<template>
  <div class="admin-dashboard">
    <div v-if="loading" class="text-center py-8">
      <div class="inline-flex items-center">
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Chargement des statistiques...
      </div>
    </div>
    
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <p>{{ error }}</p>
      <button @click="loadDashboardStats" class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
        Réessayer
      </button>
    </div>
    
    <div v-else>
      <!-- Statistiques générales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Utilisateurs</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_users }}</p>
              <p class="text-xs text-gray-500">{{ stats.new_users_today }} aujourd'hui</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Produits</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_products }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Commandes</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_orders }}</p>
              <p class="text-xs text-gray-500">{{ stats.orders_today }} aujourd'hui</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Chiffre d'affaires</p>
              <p class="text-2xl font-semibold text-gray-700">{{ formatCurrency(stats.total_revenue) }}</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Commandes récentes et Produits en rupture -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Commandes récentes -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Commandes récentes</h3>
          </div>
          <div class="p-6">
            <div v-if="stats.recent_orders && stats.recent_orders.length > 0" class="space-y-4">
              <div v-for="order in stats.recent_orders" :key="order.id" class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">#{{ order.id }} - {{ order.customer_name }}</p>
                  <p class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">{{ formatCurrency(order.total_amount) }}</p>
                  <span :class="getStatusClass(order.status)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                    {{ getStatusLabel(order.status) }}
                  </span>
                </div>
              </div>
            </div>
            <p v-else class="text-gray-500 text-sm">Aucune commande récente</p>
          </div>
        </div>
        
        <!-- Produits en rupture de stock -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Stock faible</h3>
          </div>
          <div class="p-6">
            <div v-if="stats.low_stock_products && stats.low_stock_products.length > 0" class="space-y-4">
              <div v-for="product in stats.low_stock_products" :key="`${product.id}-${product.size_name}`" class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ product.product_name }}</p>
                  <p class="text-xs text-gray-500">Taille: {{ product.size_name }}</p>
                </div>
                <div class="text-right">
                  <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                    Stock: {{ product.stock }}
                  </span>
                </div>
              </div>
            </div>
            <p v-else class="text-gray-500 text-sm">Aucun produit en stock faible</p>
          </div>
        </div>
      </div>
      
      <!-- Statuts des commandes -->
      <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Répartition des commandes par statut</h3>
        <div v-if="stats.orders_by_status" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div v-for="(count, status) in stats.orders_by_status" :key="status" class="text-center">
            <p class="text-sm text-gray-600">{{ getStatusLabel(status) }}</p>
            <p class="text-xl font-semibold" :class="getStatusTextColor(status)">{{ count }}</p>
          </div>
        </div>
      </div>
      
      <!-- Graphique des revenus mensuels -->
      <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Revenus mensuels {{ new Date().getFullYear() }}</h3>
        <div v-if="stats.monthly_revenue && stats.monthly_revenue.length > 0" class="space-y-3">
          <div v-for="month in stats.monthly_revenue" :key="month.month" class="flex items-center">
            <span class="w-20 text-sm text-gray-600">{{ getMonthName(month.month) }}</span>
            <div class="flex-1 bg-gray-200 rounded-full h-6 mr-4">
              <div 
                class="bg-purple-600 h-6 rounded-full flex items-center justify-end pr-2"
                :style="`width: ${getMonthlyRevenuePercentage(month.total)}%`"
              >
                <span class="text-xs text-white font-medium">{{ formatCurrency(month.total) }}</span>
              </div>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500 text-sm">Aucune donnée de revenus disponible</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminDashboard',
  data() {
    return {
      loading: true,
      error: null,
      stats: {
        total_users: 0,
        total_products: 0,
        total_orders: 0,
        total_revenue: 0,
        pending_orders: 0,
        recent_orders: [],
        low_stock_products: [],
        monthly_revenue: [],
        orders_by_status: {},
        new_users_today: 0,
        orders_today: 0
      }
    };
  },
  created() {
    this.loadDashboardStats();
  },
  computed: {
    maxMonthlyRevenue() {
      if (!this.stats.monthly_revenue || this.stats.monthly_revenue.length === 0) return 1;
      return Math.max(...this.stats.monthly_revenue.map(m => m.total)) || 1;
    }
  },
  methods: {
    async loadDashboardStats() {
      this.loading = true;
      this.error = null;
      
      try {
        console.log('Loading dashboard stats...');
        const response = await axios.get('/api/admin/dashboard');
        console.log('Dashboard response:', response.data);
        this.stats = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
        this.error = error.response?.data?.message || 'Impossible de charger les statistiques';
      } finally {
        this.loading = false;
      }
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount || 0);
    },
    
    formatDate(dateString) {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(new Date(dateString));
    },
    
    getStatusClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    },
    
    getStatusTextColor(status) {
      const colors = {
        pending: 'text-yellow-600',
        confirmed: 'text-blue-600',
        paid: 'text-green-600',
        shipped: 'text-purple-600',
        delivered: 'text-green-600',
        cancelled: 'text-red-600'
      };
      return colors[status] || 'text-gray-600';
    },
    
    getStatusLabel(status) {
      const labels = {
        pending: 'En attente',
        confirmed: 'Confirmée',
        paid: 'Payée',
        shipped: 'Expédiée',
        delivered: 'Livrée',
        cancelled: 'Annulée'
      };
      return labels[status] || status;
    },
    
    getMonthName(monthNumber) {
      const months = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
      ];
      return months[monthNumber - 1] || '';
    },
    
    getMonthlyRevenuePercentage(total) {
      if (this.maxMonthlyRevenue === 0) return 0;
      return Math.round((total / this.maxMonthlyRevenue) * 100);
    }
  }
};
</script>
EOL

php artisan make:seeder OrderSeeder

cat > database/seeders/OrderSeeder.php << 'EOL'
<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();
        $products = Product::with('sizes')->get();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('Aucun utilisateur ou produit trouvé. Création de commandes sans utilisateur.');
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
EOL

php artisan db:seed --class=OrderSeeder

php artisan optimize:clear

cat > app/Http/Controllers/API/AdminController.php << 'EOL'
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
            $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
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
            
            $stats = [
                'total_users' => $totalUsers,
                'total_products' => $totalProducts,
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
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
                $revenue = Order::where('status', 'paid')
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
EOL

cat > app/Models/Order.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'is_live_order',
        'notes',
        'total_amount',
        'payment_date',
    ];
    
    protected $casts = [
        'payment_date' => 'datetime',
        'is_live_order' => 'boolean',
        'total_amount' => 'decimal:2',
    ];
    
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
EOL

cat > app/Models/OrderItem.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'product_id',
        'size_id',
        'quantity',
        'price',
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
EOL

php artisan migrate:status

cat > app/Http/Controllers/API/OrderController.php << 'EOL'
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
EOL

cat > resources/js/views/admin/AdminOrders.vue << 'EOL'
<template>
  <div class="admin-orders">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-800">Gestion des commandes</h2>
    </div>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="N°, nom, email..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @input="debounceSearch"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
          <select
            v-model="filters.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadOrders"
          >
            <option value="">Tous les statuts</option>
            <option value="pending">En attente</option>
            <option value="confirmed">Confirmée</option>
            <option value="paid">Payée</option>
            <option value="shipped">Expédiée</option>
            <option value="delivered">Livrée</option>
            <option value="cancelled">Annulée</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date de</label>
          <input
            v-model="filters.date_from"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadOrders"
          >
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date à</label>
          <input
            v-model="filters.date_to"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadOrders"
          >
        </div>
      </div>
    </div>
    
    <!-- Message de chargement -->
    <div v-if="loading" class="text-center py-8">
      <p>Chargement des commandes...</p>
    </div>
    
    <!-- Message d'erreur -->
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <p>{{ error }}</p>
    </div>
    
    <!-- Message si aucune commande -->
    <div v-else-if="orders.length === 0" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
      <p>Aucune commande trouvée.</p>
    </div>
    
    <!-- Liste des commandes -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              N° Commande
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Client
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Date
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Total
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Statut
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Type
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in orders" :key="order.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              #{{ order.id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ order.customer_name }}</div>
              <div class="text-sm text-gray-500">{{ order.customer_email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(order.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatCurrency(order.total_amount) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <select
                v-model="order.status"
                @change="updateOrderStatus(order)"
                :class="getStatusClass(order.status)"
                class="text-xs font-semibold rounded-full px-3 py-1"
              >
                <option value="pending">En attente</option>
                <option value="confirmed">Confirmée</option>
                <option value="paid">Payée</option>
                <option value="shipped">Expédiée</option>
                <option value="delivered">Livrée</option>
                <option value="cancelled">Annulée</option>
              </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span v-if="order.is_live_order" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                Live
              </span>
              <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                Web
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <router-link
                :to="`/admin/orders/${order.id}`"
                class="text-indigo-600 hover:text-indigo-900"
              >
                Détails
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Précédent
          </button>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Suivant
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Affichage de
              <span class="font-medium">{{ pagination.from || 0 }}</span>
              à
              <span class="font-medium">{{ pagination.to || 0 }}</span>
              sur
              <span class="font-medium">{{ pagination.total }}</span>
              résultats
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Précédent</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
              <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                Page {{ pagination.current_page }} sur {{ pagination.last_page }}
              </span>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Suivant</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminOrders',
  data() {
    return {
      orders: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      },
      filters: {
        search: '',
        status: '',
        date_from: '',
        date_to: '',
        is_live_order: ''
      },
      loading: false,
      error: null,
      searchTimeout: null
    };
  },
  created() {
    this.loadOrders();
  },
  methods: {
    async loadOrders(page = 1) {
      this.loading = true;
      this.error = null;
      
      try {
        const params = {
          page,
          ...this.filters
        };
        
        console.log('Loading orders with params:', params);
        const response = await axios.get('/api/admin/orders', { params });
        console.log('Orders response:', response.data);
        
        this.orders = response.data.data || [];
        this.pagination = {
          current_page: response.data.current_page || 1,
          last_page: response.data.last_page || 1,
          per_page: response.data.per_page || 10,
          total: response.data.total || 0,
          from: response.data.from || 0,
          to: response.data.to || 0
        };
      } catch (error) {
        console.error('Erreur lors du chargement des commandes:', error);
        this.error = error.response?.data?.message || 'Impossible de charger les commandes';
      } finally {
        this.loading = false;
      }
    },
    
    debounceSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.loadOrders(1);
      }, 500);
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.loadOrders(page);
      }
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount || 0);
    },
    
    formatDate(dateString) {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(new Date(dateString));
    },
    
    getStatusClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    },
    
    async updateOrderStatus(order) {
      try {
        await axios.put(`/api/admin/orders/${order.id}`, {
          status: order.status
        });
        alert('Statut de la commande mis à jour');
      } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
        alert('Impossible de mettre à jour le statut');
        this.loadOrders(this.pagination.current_page);
      }
    }
  }
};
</script>
EOL

cat > app/Models/Order.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'is_live_order',
        'notes',
        'total_amount',
        'payment_date',
    ];
    
    protected $casts = [
        'payment_date' => 'datetime',
        'is_live_order' => 'boolean',
        'total_amount' => 'decimal:2',
    ];
    
    protected $with = ['items']; // Charger automatiquement les items
    
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
EOL

cat > app/Models/OrderItem.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'product_id',
        'size_id',
        'quantity',
        'price',
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
EOL

cat > database/seeders/OrderSeeder.php << 'EOL'
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
EOL

php artisan db:seed --class=OrderSeeder

cat > resources/js/views/admin/AdminProducts.vue << 'EOL'
<template>
<div class="admin-products">
<div class="flex justify-between items-center mb-6">
<h2 class="text-2xl font-semibold text-gray-800">Gestion des produits</h2>
<router-link
to="/admin/products/new"
class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
>
Ajouter un produit
</router-link>
</div>

<!-- Filtres -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
<input
v-model="filters.search"
type="text"
placeholder="Nom, référence..."
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@input="debounceSearch"
>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
<select
v-model="filters.category_id"
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@change="loadProducts"
>
<option value="">Toutes les catégories</option>
<option v-for="category in categories" :key="category.id" :value="category.id">
{{ category.name }}
</option>
</select>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
<select
v-model="filters.is_active"
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@change="loadProducts"
>
<option value="">Tous les statuts</option>
<option value="true">Actif</option>
<option value="false">Inactif</option>
</select>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
<select
v-model="filters.stock_status"
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@change="loadProducts"
>
<option value="">Tous</option>
<option value="low">Stock faible</option>
<option value="out">Rupture de stock</option>
</select>
</div>
</div>
</div>

<!-- Liste des produits -->
<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200">
<thead class="bg-gray-50">
<tr>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Produit
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Référence
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
Catégorie
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Prix
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
Stock
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Statut
</th>
<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
Actions
</th>
</tr>
</thead>
<tbody class="bg-white divide-y divide-gray-200">
<tr v-for="product in products" :key="product.id">
<td class="px-6 py-4 whitespace-nowrap">
<div class="flex items-center">
<div class="h-10 w-10 flex-shrink-0">
<img class="h-10 w-10 rounded-full object-cover" :src="product.image_url" :alt="product.name">
</div>
<div class="ml-4">
<div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
</div>
</div>
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
{{ product.reference }}
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
{{ product.category ? product.category.name : '-' }}
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
{{ formatCurrency(product.price) }}
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
<div v-if="product.sizes && product.sizes.length > 0">
<span v-for="size in product.sizes" :key="size.id" class="inline-block mr-2">
{{ size.name }}: {{ size.pivot.stock }}
</span>
</div>
<span v-else>-</span>
</td>
<td class="px-6 py-4 whitespace-nowrap">
<span :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
{{ product.is_active ? 'Actif' : 'Inactif' }}
</span>
</td>
<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
<div class="flex items-center justify-end space-x-2">
<router-link
:to="`/admin/products/${product.id}/edit`"
class="text-indigo-600 hover:text-indigo-900"
title="Modifier"
>
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
</svg>
</router-link>
<button
@click="deleteProduct(product)"
class="text-red-600 hover:text-red-900"
title="Supprimer"
>
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
</svg>
</button>
</div>
</td>
</tr>
</tbody>
</table>
<!-- Pagination -->
<div v-if="pagination.total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
<div class="flex-1 flex justify-between sm:hidden">
<button
@click="changePage(pagination.current_page - 1)"
:disabled="pagination.current_page === 1"
class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
>
Précédent
</button>
<button
@click="changePage(pagination.current_page + 1)"
:disabled="pagination.current_page === pagination.last_page"
class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
>
Suivant
</button>
</div>
<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
<div>
<p class="text-sm text-gray-700">
Affichage de
<span class="font-medium">{{ pagination.from }}</span>
à
<span class="font-medium">{{ pagination.to }}</span>
sur
<span class="font-medium">{{ pagination.total }}</span>
résultats
</p>
</div>
<div>
<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
<button
@click="changePage(pagination.current_page - 1)"
:disabled="pagination.current_page === 1"
class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
>
Précédent
</button>
<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
Page {{ pagination.current_page }} sur {{ pagination.last_page }}
</span>
<button
@click="changePage(pagination.current_page + 1)"
:disabled="pagination.current_page === pagination.last_page"
class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
>
Suivant
</button>
</nav>
</div>
</div>
</div>
</div>
</div>
</template>

<script>
import axios from 'axios';

export default {
name: 'AdminProducts',
data() {
return {
products: [],
categories: [],
pagination: {
current_page: 1,
last_page: 1,
per_page: 10,
total: 0,
from: 0,
to: 0
},
filters: {
search: '',
category_id: '',
is_active: '',
stock_status: ''
},
loading: false,
error: null,
searchTimeout: null
};
},
created() {
this.loadCategories();
this.loadProducts();
},
methods: {
async loadCategories() {
try {
const response = await axios.get('/api/admin/categories');
this.categories = response.data;
} catch (error) {
console.error('Erreur lors du chargement des catégories:', error);
}
},
async loadProducts(page = 1) {
this.loading = true;
this.error = null;
try {
const params = {
page,
...this.filters
};
const response = await axios.get('/api/admin/products', { params });
this.products = response.data.data || [];
this.pagination = {
current_page: response.data.current_page || 1,
last_page: response.data.last_page || 1,
per_page: response.data.per_page || 10,
total: response.data.total || 0,
from: response.data.from || 0,
to: response.data.to || 0
};
} catch (error) {
console.error('Erreur lors du chargement des produits:', error);
this.error = error.response?.data?.message || 'Impossible de charger les produits';
} finally {
this.loading = false;
}
},
debounceSearch() {
clearTimeout(this.searchTimeout);
this.searchTimeout = setTimeout(() => {
this.loadProducts(1);
}, 500);
},
changePage(page) {
if (page >= 1 && page <= this.pagination.last_page) {
this.loadProducts(page);
}
},
formatCurrency(amount) {
return new Intl.NumberFormat('fr-FR', {
style: 'currency',
currency: 'EUR'
}).format(amount);
},
async deleteProduct(product) {
if (!confirm(`Êtes-vous sûr de vouloir supprimer le produit "${product.name}" ?`)) {
return;
}
try {
await axios.delete(`/api/admin/products/${product.id}`);
this.loadProducts(this.pagination.current_page);
alert('Produit supprimé avec succès');
} catch (error) {
console.error('Erreur lors de la suppression du produit:', error);
alert('Impossible de supprimer le produit');
}
}
}
};
</script>
EOL

cat > resources/js/views/admin/AdminUsers.vue << 'EOL'
<template>
<div class="admin-users">
<div class="flex justify-between items-center mb-6">
<h2 class="text-2xl font-semibold text-gray-800">Gestion des utilisateurs</h2>
<button
@click="showAddModal"
class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
>
Ajouter un utilisateur
</button>
</div>

<!-- Filtres -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
<input
v-model="filters.search"
type="text"
placeholder="Nom, email..."
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@input="debounceSearch"
>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
<select
v-model="filters.is_admin"
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@change="loadUsers"
>
<option value="">Tous les utilisateurs</option>
<option value="true">Administrateurs</option>
<option value="false">Clients</option>
</select>
</div>
</div>
</div>

<!-- Liste des utilisateurs -->
<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200">
<thead class="bg-gray-50">
<tr>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Utilisateur
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Type
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
Email vérifié
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
Date d'inscription
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
Commandes
</th>
<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
Actions
</th>
</tr>
</thead>
<tbody class="bg-white divide-y divide-gray-200">
<tr v-for="user in users" :key="user.id">
<td class="px-6 py-4 whitespace-nowrap">
<div class="flex items-center">
<div>
<div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
<div class="text-sm text-gray-500">{{ user.email }}</div>
</div>
</div>
</td>
<td class="px-6 py-4 whitespace-nowrap">
<span
:class="user.is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'"
class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
>
{{ user.is_admin ? 'Admin' : 'Client' }}
</span>
</td>
<td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
<span
:class="user.email_verified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
>
{{ user.email_verified ? 'Vérifié' : 'Non vérifié' }}
</span>
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
{{ formatDate(user.created_at) }}
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
{{ user.orders_count || 0 }}
</td>
<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
<div class="flex items-center justify-end space-x-2">
<button
@click="editUser(user)"
class="text-indigo-600 hover:text-indigo-900"
title="Modifier"
>
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
</svg>
</button>
<button
v-if="user.email !== 'admin@dressingdespiplettes.com'"
@click="deleteUser(user)"
class="text-red-600 hover:text-red-900"
title="Supprimer"
>
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
</svg>
</button>
</div>
</td>
</tr>
</tbody>
</table>
<!-- Pagination -->
<div v-if="pagination.total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
<div>
<p class="text-sm text-gray-700">
Affichage de
<span class="font-medium">{{ pagination.from }}</span>
à
<span class="font-medium">{{ pagination.to }}</span>
sur
<span class="font-medium">{{ pagination.total }}</span>
résultats
</p>
</div>
<div>
<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
<button
@click="changePage(pagination.current_page - 1)"
:disabled="pagination.current_page === 1"
class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
>
Précédent
</button>
<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
Page {{ pagination.current_page }} sur {{ pagination.last_page }}
</span>
<button
@click="changePage(pagination.current_page + 1)"
:disabled="pagination.current_page === pagination.last_page"
class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
>
Suivant
</button>
</nav>
</div>
</div>
</div>
</div>

<!-- Modal d'ajout/édition -->
<div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto p-4">
<div class="flex items-center justify-center min-h-screen">
<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
<div class="relative bg-white rounded-lg max-w-lg w-full mx-auto">
<form @submit.prevent="saveUser">
<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
<h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
{{ editingUser.id ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur' }}
</h3>
<div class="space-y-4">
<div>
<label for="user-name" class="block text-sm font-medium text-gray-700">Nom</label>
<input
id="user-name"
v-model="editingUser.name"
type="text"
required
class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
>
</div>
<div>
<label for="user-email" class="block text-sm font-medium text-gray-700">Email</label>
<input
id="user-email"
v-model="editingUser.email"
type="email"
required
class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
>
</div>
<div>
<label for="user-password" class="block text-sm font-medium text-gray-700">
{{ editingUser.id ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe' }}
</label>
<input
id="user-password"
v-model="editingUser.password"
type="password"
:required="!editingUser.id"
class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
>
</div>
<div class="flex items-center">
<input
id="user-is-admin"
v-model="editingUser.is_admin"
type="checkbox"
class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
>
<label for="user-is-admin" class="ml-2 block text-sm text-gray-900">
Administrateur
</label>
</div>
<div class="flex items-center">
<input
id="user-email-verified"
v-model="editingUser.email_verified"
type="checkbox"
class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
>
<label for="user-email-verified" class="ml-2 block text-sm text-gray-900">
Email vérifié
</label>
</div>
</div>
</div>
<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
<button
type="submit"
:disabled="saving"
class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm"
>
{{ saving ? 'Enregistrement...' : 'Enregistrer' }}
</button>
<button
type="button"
@click="closeModal"
class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
>
Annuler
</button>
</div>
</form>
</div>
</div>
</div>
</div>
</template>

<script>
import axios from 'axios';

export default {
name: 'AdminUsers',
data() {
return {
users: [],
pagination: {
current_page: 1,
last_page: 1,
per_page: 10,
total: 0,
from: 0,
to: 0
},
filters: {
search: '',
is_admin: ''
},
loading: false,
error: null,
showModal: false,
editingUser: {
id: null,
name: '',
email: '',
password: '',
is_admin: false,
email_verified: true
},
saving: false,
searchTimeout: null
};
},
created() {
this.loadUsers();
},
methods: {
async loadUsers(page = 1) {
this.loading = true;
this.error = null;
try {
const params = {
page,
...this.filters
};
const response = await axios.get('/api/admin/users', { params });
this.users = response.data.data;
this.pagination = {
current_page: response.data.current_page,
last_page: response.data.last_page,
per_page: response.data.per_page,
total: response.data.total,
from: response.data.from || 0,
to: response.data.to || 0
};
} catch (error) {
console.error('Erreur lors du chargement des utilisateurs:', error);
this.error = error.response?.data?.message || 'Impossible de charger les utilisateurs';
} finally {
this.loading = false;
}
},
debounceSearch() {
clearTimeout(this.searchTimeout);
this.searchTimeout = setTimeout(() => {
this.loadUsers(1);
}, 500);
},
changePage(page) {
if (page >= 1 && page <= this.pagination.last_page) {
this.loadUsers(page);
}
},
formatDate(dateString) {
return new Intl.DateTimeFormat('fr-FR', {
day: '2-digit',
month: '2-digit',
year: 'numeric'
}).format(new Date(dateString));
},
showAddModal() {
this.editingUser = {
id: null,
name: '',
email: '',
password: '',
is_admin: false,
email_verified: true
};
this.showModal = true;
},
editUser(user) {
this.editingUser = {
...user,
password: ''
};
this.showModal = true;
},
closeModal() {
this.showModal = false;
this.editingUser = {
id: null,
name: '',
email: '',
password: '',
is_admin: false,
email_verified: true
};
},
async saveUser() {
this.saving = true;
try {
const data = {
name: this.editingUser.name,
email: this.editingUser.email,
is_admin: this.editingUser.is_admin,
email_verified: this.editingUser.email_verified
};
if (this.editingUser.password) {
data.password = this.editingUser.password;
}

if (this.editingUser.id) {
await axios.put(`/api/admin/users/${this.editingUser.id}`, data);
alert('Utilisateur mis à jour avec succès');
} else {
await axios.post('/api/admin/users', data);
alert('Utilisateur créé avec succès');
}
this.closeModal();
this.loadUsers(this.pagination.current_page);
} catch (error) {
console.error('Erreur lors de l\'enregistrement de l\'utilisateur:', error);
alert('Impossible d\'enregistrer l\'utilisateur');
} finally {
this.saving = false;
}
},
async deleteUser(user) {
if (!confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${user.name}" ?`)) {
return;
}
try {
await axios.delete(`/api/admin/users/${user.id}`);
this.loadUsers(this.pagination.current_page);
alert('Utilisateur supprimé avec succès');
} catch (error) {
console.error('Erreur lors de la suppression de l\'utilisateur:', error);
if (error.response && error.response.status === 403) {
alert('Impossible de supprimer cet utilisateur. Il s\'agit peut-être du compte administrateur principal.');
} else {
alert('Impossible de supprimer l\'utilisateur');
}
}
}
}
};
</script>
EOL

cat > resources/js/views/admin/AdminCategories.vue << 'EOL'
<template>
<div class="admin-categories">
<div class="flex justify-between items-center mb-6">
<h2 class="text-2xl font-semibold text-gray-800">Gestion des catégories</h2>
<button
@click="showAddModal"
class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
>
Ajouter une catégorie
</button>
</div>

<!-- Liste des catégories -->
<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200">
<thead class="bg-gray-50">
<tr>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Nom
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
Description
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Nombre de produits
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Statut
</th>
<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
Actions
</th>
</tr>
</thead>
<tbody class="bg-white divide-y divide-gray-200">
<tr v-for="category in categories" :key="category.id">
<td class="px-6 py-4 whitespace-nowrap">
<div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
</td>
<td class="px-6 py-4 hidden sm:table-cell">
<div class="text-sm text-gray-500">{{ category.description || '-' }}</div>
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
{{ category.products_count || 0 }}
</td>
<td class="px-6 py-4 whitespace-nowrap">
<span
:class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
>
{{ category.is_active ? 'Actif' : 'Inactif' }}
</span>
</td>
<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
<div class="flex items-center justify-end space-x-2">
<button
@click="editCategory(category)"
class="text-indigo-600 hover:text-indigo-900"
title="Modifier"
>
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
</svg>
</button>
<button
@click="deleteCategory(category)"
class="text-red-600 hover:text-red-900"
title="Supprimer"
>
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
</svg>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>

<!-- Modal d'ajout/édition -->
<div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto p-4">
<div class="flex items-center justify-center min-h-screen">
<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
<div class="relative bg-white rounded-lg max-w-lg w-full mx-auto">
<form @submit.prevent="saveCategory">
<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
<h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
{{ editingCategory.id ? 'Modifier la catégorie' : 'Ajouter une catégorie' }}
</h3>
<div class="space-y-4">
<div>
<label for="category-name" class="block text-sm font-medium text-gray-700">Nom</label>
<input
id="category-name"
v-model="editingCategory.name"
type="text"
required
class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
>
</div>
<div>
<label for="category-description" class="block text-sm font-medium text-gray-700">Description</label>
<textarea
id="category-description"
v-model="editingCategory.description"
rows="3"
class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
></textarea>
</div>
<div class="flex items-center">
<input
id="category-active"
v-model="editingCategory.is_active"
type="checkbox"
class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
>
<label for="category-active" class="ml-2 block text-sm text-gray-900">
Catégorie active
</label>
</div>
</div>
</div>
<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
<button
type="submit"
:disabled="saving"
class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm"
>
{{ saving ? 'Enregistrement...' : 'Enregistrer' }}
</button>
<button
type="button"
@click="closeModal"
class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
>
Annuler
</button>
</div>
</form>
</div>
</div>
</div>
</div>
</template>

<script>
import axios from 'axios';

export default {
name: 'AdminCategories',
data() {
return {
categories: [],
showModal: false,
editingCategory: {
id: null,
name: '',
description: '',
is_active: true
},
saving: false
};
},
created() {
this.loadCategories();
},
methods: {
async loadCategories() {
try {
const response = await axios.get('/api/admin/categories');
this.categories = response.data;
} catch (error) {
console.error('Erreur lors du chargement des catégories:', error);
alert('Impossible de charger les catégories');
}
},
showAddModal() {
this.editingCategory = {
id: null,
name: '',
description: '',
is_active: true
};
this.showModal = true;
},
editCategory(category) {
this.editingCategory = { ...category };
this.showModal = true;
},
closeModal() {
this.showModal = false;
this.editingCategory = {
id: null,
name: '',
description: '',
is_active: true
};
},
async saveCategory() {
this.saving = true;
try {
if (this.editingCategory.id) {
await axios.put(`/api/admin/categories/${this.editingCategory.id}`, this.editingCategory);
alert('Catégorie mise à jour avec succès');
} else {
await axios.post('/api/admin/categories', this.editingCategory);
alert('Catégorie créée avec succès');
}
this.closeModal();
this.loadCategories();
} catch (error) {
console.error('Erreur lors de l\'enregistrement de la catégorie:', error);
alert('Impossible d\'enregistrer la catégorie');
} finally {
this.saving = false;
}
},
async deleteCategory(category) {
if (!confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${category.name}" ?`)) {
return;
}
try {
await axios.delete(`/api/admin/categories/${category.id}`);
this.loadCategories();
alert('Catégorie supprimée avec succès');
} catch (error) {
console.error('Erreur lors de la suppression de la catégorie:', error);
if (error.response && error.response.status === 422) {
alert('Impossible de supprimer cette catégorie car elle contient des produits.');
} else {
alert('Impossible de supprimer la catégorie');
}
}
}
}
};
</script>
EOL

cat > resources/js/layouts/AdminLayout.vue << 'EOL'
<template>
<div class="flex h-screen bg-gray-100">
<!-- Overlay for mobile -->
<div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black opacity-50 md:hidden"></div>
    
<!-- Sidebar -->
<div :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', 'md:translate-x-0']" 
     class="fixed md:static inset-y-0 left-0 z-30 w-64 bg-gray-800 transition-transform duration-300 ease-in-out">
  <div class="flex flex-col h-full">
    <div class="flex items-center justify-between h-16 bg-gray-900 px-4">
      <h1 class="text-white text-lg font-semibold">Administration</h1>
      <button @click="sidebarOpen = false" class="md:hidden text-white hover:text-gray-300">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
      <router-link
        to="/admin/dashboard"
        class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
        :class="{ 'bg-gray-700 text-white': $route.path === '/admin/dashboard' }"
        @click="sidebarOpen = false"
      >
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
      </router-link>
      <router-link
        to="/admin/products"
        class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
        :class="{ 'bg-gray-700 text-white': $route.path.startsWith('/admin/products') }"
        @click="sidebarOpen = false"
      >
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        Produits
      </router-link>
      <router-link
        to="/admin/orders"
        class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
        :class="{ 'bg-gray-700 text-white': $route.path.startsWith('/admin/orders') }"
        @click="sidebarOpen = false"
      >
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
        </svg>
        Commandes
      </router-link>
      <router-link
        to="/admin/users"
        class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
        :class="{ 'bg-gray-700 text-white': $route.path.startsWith('/admin/users') }"
        @click="sidebarOpen = false"
      >
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        Utilisateurs
      </router-link>
      <router-link
        to="/admin/categories"
        class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
        :class="{ 'bg-gray-700 text-white': $route.path.startsWith('/admin/categories') }"
        @click="sidebarOpen = false"
      >
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
        </svg>
        Catégories
      </router-link>
    </nav>
    <div class="p-4">
      <router-link
        to="/"
        class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md"
        @click="sidebarOpen = false"
      >
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path>
        </svg>
        Retour au site
      </router-link>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="flex-1 flex flex-col overflow-hidden">
  <!-- Header -->
  <header class="bg-white shadow-sm">
    <div class="flex items-center justify-between h-16 px-6">
      <button @click="sidebarOpen = true" class="md:hidden">
        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
      <h2 class="text-xl font-semibold text-gray-800">{{ pageTitle }}</h2>
      <div class="flex items-center space-x-4">
        <span class="text-gray-600 hidden sm:block">{{ user.name }}</span>
        <button
          @click="logout"
          class="text-gray-500 hover:text-gray-700"
          title="Déconnexion"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
        </button>
      </div>
    </div>
  </header>
  
  <!-- Page Content -->
  <main class="flex-1 overflow-y-auto p-4 md:p-6">
    <router-view></router-view>
  </main>
</div>
</div>
</template>

<script>
import axios from 'axios';

export default {
name: 'AdminLayout',
data() {
  return {
    user: {},
    sidebarOpen: false
  };
},
computed: {
  pageTitle() {
    const titles = {
      '/admin/dashboard': 'Dashboard',
      '/admin/products': 'Gestion des produits',
      '/admin/orders': 'Gestion des commandes',
      '/admin/users': 'Gestion des utilisateurs',
      '/admin/categories': 'Gestion des catégories'
    };
    
    // Chercher une correspondance exacte d'abord
    if (titles[this.$route.path]) {
      return titles[this.$route.path];
    }
    
    // Ensuite chercher une correspondance partielle
    for (const path in titles) {
      if (this.$route.path.startsWith(path)) {
        return titles[path];
      }
    }
    
    return 'Administration';
  }
},
created() {
  this.loadUser();
},
methods: {
  loadUser() {
    const userJson = localStorage.getItem('user');
    if (userJson) {
      this.user = JSON.parse(userJson);
    }
  },
  async logout() {
    try {
      await axios.post('/api/logout');
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      delete axios.defaults.headers.common['Authorization'];
      window.dispatchEvent(new CustomEvent('auth-changed'));
      this.$router.push('/');
    } catch (error) {
      console.error('Erreur lors de la déconnexion:', error);
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      delete axios.defaults.headers.common['Authorization'];
      window.dispatchEvent(new CustomEvent('auth-changed'));
      this.$router.push('/');
    }
  }
}
};
</script>
EOL

cat >> resources/css/app.css << 'EOL'

/* Responsive tables */
@media (max-width: 640px) {
  table {
    font-size: 0.875rem;
  }
  
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  th, td {
    padding: 0.5rem;
  }
}

/* Responsive modals */
.modal-container {
  max-height: 90vh;
  overflow-y: auto;
}

/* Responsive layout */
@media (max-width: 768px) {
  .admin-layout .sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
  }
  
  .admin-layout .sidebar.open {
    transform: translateX(0);
  }
}

/* Amélioration des formulaires sur mobile */
input[type="text"],
input[type="email"],
input[type="password"],
input[type="number"],
textarea,
select {
  font-size: 16px; /* Empêche le zoom sur iOS */
}

/* Responsive cards */
@media (max-width: 640px) {
  .card {
    padding: 1rem;
  }
}
EOL

cat > app/Http/Controllers/API/AdminController.php << 'EOL'
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
EOL

cat > resources/js/views/admin/AdminDashboard.vue << 'EOL'
<template>
  <div class="admin-dashboard">
    <div v-if="loading" class="text-center py-8">
      <div class="inline-flex items-center">
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Chargement des statistiques...
      </div>
    </div>
    
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <p>{{ error }}</p>
      <button @click="loadDashboardStats" class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
        Réessayer
      </button>
    </div>
    
    <div v-else>
      <!-- Bouton de rafraîchissement -->
      <div class="mb-4 flex justify-end">
        <button 
          @click="loadDashboardStats" 
          :disabled="refreshing"
          class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 disabled:opacity-50"
        >
          <svg v-if="refreshing" class="animate-spin h-4 w-4 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Rafraîchir
        </button>
      </div>
      
      <!-- Statistiques générales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Utilisateurs</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_users }}</p>
              <p class="text-xs text-gray-500">{{ stats.new_users_today }} aujourd'hui</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Produits</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_products }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Commandes</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_orders }}</p>
              <p class="text-xs text-gray-500">{{ stats.orders_today }} aujourd'hui</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Chiffre d'affaires total</p>
              <p class="text-2xl font-semibold text-gray-700">{{ formatCurrency(stats.total_revenue) }}</p>
              <p class="text-xs text-gray-500">{{ formatCurrency(stats.revenue_today) }} aujourd'hui</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Statistiques de revenus supplémentaires -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-sm font-medium text-gray-600 mb-2">CA aujourd'hui</h3>
          <p class="text-xl font-semibold text-gray-700">{{ formatCurrency(stats.revenue_today) }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-sm font-medium text-gray-600 mb-2">CA cette semaine</h3>
          <p class="text-xl font-semibold text-gray-700">{{ formatCurrency(stats.revenue_this_week) }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-sm font-medium text-gray-600 mb-2">CA ce mois</h3>
          <p class="text-xl font-semibold text-gray-700">{{ formatCurrency(stats.revenue_this_month) }}</p>
        </div>
      </div>
      
      <!-- Commandes récentes et Produits en rupture -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Commandes récentes -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Commandes récentes</h3>
          </div>
          <div class="p-6">
            <div v-if="stats.recent_orders && stats.recent_orders.length > 0" class="space-y-4">
              <div v-for="order in stats.recent_orders" :key="order.id" class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">#{{ order.id }} - {{ order.customer_name }}</p>
                  <p class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">{{ formatCurrency(order.total_amount) }}</p>
                  <span :class="getStatusClass(order.status)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                    {{ getStatusLabel(order.status) }}
                  </span>
                </div>
              </div>
            </div>
            <p v-else class="text-gray-500 text-sm">Aucune commande récente</p>
          </div>
        </div>
        
        <!-- Produits en rupture de stock -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Stock faible</h3>
          </div>
          <div class="p-6">
            <div v-if="stats.low_stock_products && stats.low_stock_products.length > 0" class="space-y-4">
              <div v-for="product in stats.low_stock_products" :key="`${product.id}-${product.size_name}`" class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ product.product_name }}</p>
                  <p class="text-xs text-gray-500">Taille: {{ product.size_name }}</p>
                </div>
                <div class="text-right">
                  <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                    Stock: {{ product.stock }}
                  </span>
                </div>
              </div>
            </div>
            <p v-else class="text-gray-500 text-sm">Aucun produit en stock faible</p>
          </div>
        </div>
      </div>
      
      <!-- Statuts des commandes -->
      <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Répartition des commandes par statut</h3>
        <div v-if="stats.orders_by_status" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div v-for="(count, status) in stats.orders_by_status" :key="status" class="text-center">
            <p class="text-sm text-gray-600">{{ getStatusLabel(status) }}</p>
            <p class="text-xl font-semibold" :class="getStatusTextColor(status)">{{ count }}</p>
          </div>
        </div>
      </div>
      
      <!-- Graphique des revenus mensuels -->
      <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Revenus mensuels {{ new Date().getFullYear() }}</h3>
        <div v-if="stats.monthly_revenue && stats.monthly_revenue.length > 0" class="space-y-3">
          <div v-for="month in stats.monthly_revenue" :key="month.month" class="flex items-center">
            <span class="w-20 text-sm text-gray-600">{{ getMonthName(month.month) }}</span>
            <div class="flex-1 bg-gray-200 rounded-full h-6 mr-4">
              <div
                class="bg-purple-600 h-6 rounded-full flex items-center justify-end pr-2"
                :style="`width: ${getMonthlyRevenuePercentage(month.total)}%`"
              >
                <span class="text-xs text-white font-medium">{{ formatCurrency(month.total) }}</span>
              </div>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500 text-sm">Aucune donnée de revenus disponible</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminDashboard',
  data() {
    return {
      loading: true,
      refreshing: false,
      error: null,
      stats: {
        total_users: 0,
        total_products: 0,
        total_orders: 0,
        total_revenue: 0,
        revenue_today: 0,
        revenue_this_week: 0,
        revenue_this_month: 0,
        pending_orders: 0,
        recent_orders: [],
        low_stock_products: [],
        monthly_revenue: [],
        orders_by_status: {},
        new_users_today: 0,
        orders_today: 0
      },
      refreshInterval: null
    };
  },
  
  mounted() {
    this.loadDashboardStats();
    // Rafraîchir automatiquement toutes les 30 secondes
    this.refreshInterval = setInterval(() => {
      this.loadDashboardStats(true);
    }, 30000);
  },
  
  beforeUnmount() {
    if (this.refreshInterval) {
      clearInterval(this.refreshInterval);
    }
  },
  
  computed: {
    maxMonthlyRevenue() {
      if (!this.stats.monthly_revenue || this.stats.monthly_revenue.length === 0) return 1;
      return Math.max(...this.stats.monthly_revenue.map(m => m.total)) || 1;
    }
  },
  
  methods: {
    async loadDashboardStats(isRefresh = false) {
      if (isRefresh) {
        this.refreshing = true;
      } else {
        this.loading = true;
      }
      this.error = null;
      
      try {
        console.log('Loading dashboard stats...');
        const response = await axios.get('/api/admin/dashboard');
        console.log('Dashboard response:', response.data);
        this.stats = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
        this.error = error.response?.data?.message || 'Impossible de charger les statistiques';
      } finally {
        this.loading = false;
        this.refreshing = false;
      }
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount || 0);
    },
    
    formatDate(dateString) {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(new Date(dateString));
    },
    
    getStatusClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    },
    
    getStatusTextColor(status) {
      const colors = {
        pending: 'text-yellow-600',
        confirmed: 'text-blue-600',
        paid: 'text-green-600',
        shipped: 'text-purple-600',
        delivered: 'text-green-600',
        cancelled: 'text-red-600'
      };
      return colors[status] || 'text-gray-600';
    },
    
    getStatusLabel(status) {
      const labels = {
        pending: 'En attente',
        confirmed: 'Confirmée',
        paid: 'Payée',
        shipped: 'Expédiée',
        delivered: 'Livrée',
        cancelled: 'Annulée'
      };
      return labels[status] || status;
    },
    
    getMonthName(monthNumber) {
      const months = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
      ];
      return months[monthNumber - 1] || '';
    },
    
    getMonthlyRevenuePercentage(total) {
      if (this.maxMonthlyRevenue === 0) return 0;
      return Math.round((total / this.maxMonthlyRevenue) * 100);
    }
  }
};
</script>
EOL

cat > resources/js/utils/EventBus.js << 'EOL'
// EventBus pour la communication entre composants
export default {
  callbacks: {},
  
  on(event, callback) {
    if (!this.callbacks[event]) {
      this.callbacks[event] = [];
    }
    this.callbacks[event].push(callback);
  },
  
  off(event, callback) {
    if (this.callbacks[event]) {
      this.callbacks[event] = this.callbacks[event].filter(cb => cb !== callback);
    }
  },
  
  emit(event, ...args) {
    if (this.callbacks[event]) {
      this.callbacks[event].forEach(callback => callback(...args));
    }
  }
};
EOL

cat > resources/js/views/admin/AdminOrders.vue << 'EOL'
<template>
  <div class="admin-orders">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold text-gray-800">Gestion des commandes</h2>
    </div>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="N°, nom, email..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @input="debounceSearch"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
          <select
            v-model="filters.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadOrders"
          >
            <option value="">Tous les statuts</option>
            <option value="pending">En attente</option>
            <option value="confirmed">Confirmée</option>
            <option value="paid">Payée</option>
            <option value="shipped">Expédiée</option>
            <option value="delivered">Livrée</option>
            <option value="cancelled">Annulée</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date de</label>
          <input
            v-model="filters.date_from"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadOrders"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date à</label>
          <input
            v-model="filters.date_to"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            @change="loadOrders"
          >
        </div>
      </div>
    </div>
    
    <!-- Message de chargement -->
    <div v-if="loading" class="text-center py-8">
      <p>Chargement des commandes...</p>
    </div>
    
    <!-- Message d'erreur -->
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <p>{{ error }}</p>
    </div>
    
    <!-- Message si aucune commande -->
    <div v-else-if="orders.length === 0" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
      <p>Aucune commande trouvée.</p>
    </div>
    
    <!-- Liste des commandes -->
    <div v-else class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              N° Commande
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Client
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Date
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Total
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Statut
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Type
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in orders" :key="order.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              #{{ order.id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ order.customer_name }}</div>
              <div class="text-sm text-gray-500">{{ order.customer_email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(order.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatCurrency(order.total_amount) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <select
                v-model="order.status"
                @change="updateOrderStatus(order)"
                :class="getStatusClass(order.status)"
                class="text-xs font-semibold rounded-full px-3 py-1"
              >
                <option value="pending">En attente</option>
                <option value="confirmed">Confirmée</option>
                <option value="paid">Payée</option>
                <option value="shipped">Expédiée</option>
                <option value="delivered">Livrée</option>
                <option value="cancelled">Annulée</option>
              </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span v-if="order.is_live_order" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                Live
              </span>
              <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                Web
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <router-link
                :to="`/admin/orders/${order.id}`"
                class="text-indigo-600 hover:text-indigo-900"
              >
                Détails
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
      
      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Précédent
          </button>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Suivant
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Affichage de
              <span class="font-medium">{{ pagination.from || 0 }}</span>
              à
              <span class="font-medium">{{ pagination.to || 0 }}</span>
              sur
              <span class="font-medium">{{ pagination.total }}</span>
              résultats
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Précédent</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
              <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                Page {{ pagination.current_page }} sur {{ pagination.last_page }}
              </span>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Suivant</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import EventBus from '../../utils/EventBus';

export default {
  name: 'AdminOrders',
  data() {
    return {
      orders: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      },
      filters: {
        search: '',
        status: '',
        date_from: '',
        date_to: '',
        is_live_order: ''
      },
      loading: false,
      error: null,
      searchTimeout: null
    };
  },
  
  created() {
    this.loadOrders();
  },
  
  methods: {
    async loadOrders(page = 1) {
      this.loading = true;
      this.error = null;
      
      try {
        const params = {
          page,
          ...this.filters
        };
        
        console.log('Loading orders with params:', params);
        const response = await axios.get('/api/admin/orders', { params });
        console.log('Orders response:', response.data);
        
        this.orders = response.data.data || [];
        this.pagination = {
          current_page: response.data.current_page || 1,
          last_page: response.data.last_page || 1,
          per_page: response.data.per_page || 10,
          total: response.data.total || 0,
          from: response.data.from || 0,
          to: response.data.to || 0
        };
      } catch (error) {
        console.error('Erreur lors du chargement des commandes:', error);
        this.error = error.response?.data?.message || 'Impossible de charger les commandes';
      } finally {
        this.loading = false;
      }
    },
    
    debounceSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.loadOrders(1);
      }, 500);
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.loadOrders(page);
      }
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount || 0);
    },
    
    formatDate(dateString) {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(new Date(dateString));
    },
    
    getStatusClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    },
    
    async updateOrderStatus(order) {
      try {
        await axios.put(`/api/admin/orders/${order.id}`, {
          status: order.status
        });
        
        // Émettre un événement pour notifier le dashboard
        EventBus.emit('order-status-updated', order);
        
        // Notification de succès
        alert(`Statut de la commande #${order.id} mis à jour en "${this.getStatusLabel(order.status)}"`);
        
      } catch (error) {
        console.error('Erreur lors de la mise à jour du statut:', error);
        alert('Impossible de mettre à jour le statut');
        this.loadOrders(this.pagination.current_page);
      }
    },
    
    getStatusLabel(status) {
      const labels = {
        pending: 'En attente',
        confirmed: 'Confirmée',
        paid: 'Payée',
        shipped: 'Expédiée',
        delivered: 'Livrée',
        cancelled: 'Annulée'
      };
      return labels[status] || status;
    }
  }
};
</script>
EOL

cat > resources/js/views/admin/AdminDashboard.vue << 'EOL'
<template>
  <div class="admin-dashboard">
    <div v-if="loading" class="text-center py-8">
      <div class="inline-flex items-center">
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Chargement des statistiques...
      </div>
    </div>
    
    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <p>{{ error }}</p>
      <button @click="loadDashboardStats" class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
        Réessayer
      </button>
    </div>
    
    <div v-else>
      <!-- Bouton de rafraîchissement -->
      <div class="mb-4 flex justify-end">
        <button 
          @click="loadDashboardStats" 
          :disabled="refreshing"
          class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 disabled:opacity-50 flex items-center"
        >
          <svg v-if="refreshing" class="animate-spin h-4 w-4 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Rafraîchir
        </button>
      </div>
      
      <!-- Notification de mise à jour -->
      <div v-if="showUpdateNotification" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded flex items-center justify-between">
        <p>Les statistiques ont été mises à jour</p>
        <button @click="showUpdateNotification = false" class="text-green-700">
          <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
      
      <!-- Statistiques générales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Utilisateurs</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_users }}</p>
              <p class="text-xs text-gray-500">{{ stats.new_users_today }} aujourd'hui</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Produits</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_products }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Total Commandes</p>
              <p class="text-2xl font-semibold text-gray-700">{{ stats.total_orders }}</p>
              <p class="text-xs text-gray-500">{{ stats.orders_today }} aujourd'hui</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Chiffre d'affaires total</p>
              <p class="text-2xl font-semibold text-gray-700">{{ formatCurrency(stats.total_revenue) }}</p>
              <p class="text-xs text-gray-500">{{ formatCurrency(stats.revenue_today) }} aujourd'hui</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Statistiques de revenus supplémentaires -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-sm font-medium text-gray-600 mb-2">CA aujourd'hui</h3>
          <p class="text-xl font-semibold text-gray-700">{{ formatCurrency(stats.revenue_today) }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-sm font-medium text-gray-600 mb-2">CA cette semaine</h3>
          <p class="text-xl font-semibold text-gray-700">{{ formatCurrency(stats.revenue_this_week) }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-sm font-medium text-gray-600 mb-2">CA ce mois</h3>
          <p class="text-xl font-semibold text-gray-700">{{ formatCurrency(stats.revenue_this_month) }}</p>
        </div>
      </div>
      
      <!-- Commandes récentes et Produits en rupture -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Commandes récentes -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Commandes récentes</h3>
          </div>
          <div class="p-6">
            <div v-if="stats.recent_orders && stats.recent_orders.length > 0" class="space-y-4">
              <div v-for="order in stats.recent_orders" :key="order.id" class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">#{{ order.id }} - {{ order.customer_name }}</p>
                  <p class="text-xs text-gray-500">{{ formatDate(order.created_at) }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">{{ formatCurrency(order.total_amount) }}</p>
                  <span :class="getStatusClass(order.status)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                    {{ getStatusLabel(order.status) }}
                  </span>
                </div>
              </div>
            </div>
            <p v-else class="text-gray-500 text-sm">Aucune commande récente</p>
          </div>
        </div>
        
        <!-- Produits en rupture de stock -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Stock faible</h3>
          </div>
          <div class="p-6">
            <div v-if="stats.low_stock_products && stats.low_stock_products.length > 0" class="space-y-4">
              <div v-for="product in stats.low_stock_products" :key="`${product.id}-${product.size_name}`" class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ product.product_name }}</p>
                  <p class="text-xs text-gray-500">Taille: {{ product.size_name }}</p>
                </div>
                <div class="text-right">
                  <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                    Stock: {{ product.stock }}
                  </span>
                </div>
              </div>
            </div>
            <p v-else class="text-gray-500 text-sm">Aucun produit en stock faible</p>
          </div>
        </div>
      </div>
      
      <!-- Statuts des commandes -->
      <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Répartition des commandes par statut</h3>
        <div v-if="stats.orders_by_status" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div v-for="(count, status) in stats.orders_by_status" :key="status" class="text-center">
            <p class="text-sm text-gray-600">{{ getStatusLabel(status) }}</p>
            <p class="text-xl font-semibold" :class="getStatusTextColor(status)">{{ count }}</p>
          </div>
        </div>
      </div>
      
      <!-- Graphique des revenus mensuels -->
      <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Revenus mensuels {{ new Date().getFullYear() }}</h3>
        <div v-if="stats.monthly_revenue && stats.monthly_revenue.length > 0" class="space-y-3">
          <div v-for="month in stats.monthly_revenue" :key="month.month" class="flex items-center">
            <span class="w-20 text-sm text-gray-600">{{ getMonthName(month.month) }}</span>
            <div class="flex-1 bg-gray-200 rounded-full h-6 mr-4">
              <div
                class="bg-purple-600 h-6 rounded-full flex items-center justify-end pr-2"
                :style="`width: ${getMonthlyRevenuePercentage(month.total)}%`"
              >
                <span class="text-xs text-white font-medium">{{ formatCurrency(month.total) }}</span>
              </div>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500 text-sm">Aucune donnée de revenus disponible</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import EventBus from '../../utils/EventBus';

export default {
  name: 'AdminDashboard',
  data() {
    return {
      loading: true,
      refreshing: false,
      error: null,
      showUpdateNotification: false,
      stats: {
        total_users: 0,
        total_products: 0,
        total_orders: 0,
        total_revenue: 0,
        revenue_today: 0,
        revenue_this_week: 0,
        revenue_this_month: 0,
        pending_orders: 0,
        recent_orders: [],
        low_stock_products: [],
        monthly_revenue: [],
        orders_by_status: {},
        new_users_today: 0,
        orders_today: 0
      },
      refreshInterval: null
    };
  },
  
  mounted() {
    this.loadDashboardStats();
    
    // Écouter les événements de mise à jour de statut
    EventBus.on('order-status-updated', this.handleOrderStatusUpdate);
    
    // Rafraîchir automatiquement toutes les 60 secondes
    this.refreshInterval = setInterval(() => {
      this.loadDashboardStats(true);
    }, 60000);
  },
  
  beforeUnmount() {
    // Nettoyer les écouteurs d'événements
    EventBus.off('order-status-updated', this.handleOrderStatusUpdate);
    
    if (this.refreshInterval) {
      clearInterval(this.refreshInterval);
    }
  },
  
  computed: {
    maxMonthlyRevenue() {
      if (!this.stats.monthly_revenue || this.stats.monthly_revenue.length === 0) return 1;
      return Math.max(...this.stats.monthly_revenue.map(m => m.total)) || 1;
    }
  },
  
  methods: {
    async loadDashboardStats(isRefresh = false) {
      if (isRefresh) {
        this.refreshing = true;
      } else {
        this.loading = true;
      }
      this.error = null;
      
      try {
        console.log('Loading dashboard stats...');
        const response = await axios.get('/api/admin/dashboard');
        console.log('Dashboard response:', response.data);
        this.stats = response.data;
        
        if (isRefresh) {
          this.showUpdateNotification = true;
          setTimeout(() => {
            this.showUpdateNotification = false;
          }, 3000);
        }
      } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
        this.error = error.response?.data?.message || 'Impossible de charger les statistiques';
      } finally {
        this.loading = false;
        this.refreshing = false;
      }
    },
    
    handleOrderStatusUpdate(order) {
      console.log('Order status updated:', order);
      // Rafraîchir les statistiques après un changement de statut
      this.loadDashboardStats(true);
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount || 0);
    },
    
    formatDate(dateString) {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(new Date(dateString));
    },
    
    getStatusClass(status) {
      const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        shipped: 'bg-purple-100 text-purple-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      };
      return classes[status] || 'bg-gray-100 text-gray-800';
    },
    
    getStatusTextColor(status) {
      const colors = {
        pending: 'text-yellow-600',
        confirmed: 'text-blue-600',
        paid: 'text-green-600',
        shipped: 'text-purple-600',
        delivered: 'text-green-600',
        cancelled: 'text-red-600'
      };
      return colors[status] || 'text-gray-600';
    },
    
    getStatusLabel(status) {
      const labels = {
        pending: 'En attente',
        confirmed: 'Confirmée',
        paid: 'Payée',
        shipped: 'Expédiée',
        delivered: 'Livrée',
        cancelled: 'Annulée'
      };
      return labels[status] || status;
    },
    
    getMonthName(monthNumber) {
      const months = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
      ];
      return months[monthNumber - 1] || '';
    },
    
    getMonthlyRevenuePercentage(total) {
      if (this.maxMonthlyRevenue === 0) return 0;
      return Math.round((total / this.maxMonthlyRevenue) * 100);
    }
  }
};
</script>
EOL



