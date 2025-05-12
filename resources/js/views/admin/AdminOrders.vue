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
