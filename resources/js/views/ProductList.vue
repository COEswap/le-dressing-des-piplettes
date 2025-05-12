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
