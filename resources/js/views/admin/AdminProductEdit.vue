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
