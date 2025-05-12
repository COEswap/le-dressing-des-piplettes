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
