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
