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
