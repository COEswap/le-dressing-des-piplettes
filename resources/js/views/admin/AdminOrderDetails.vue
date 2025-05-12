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
