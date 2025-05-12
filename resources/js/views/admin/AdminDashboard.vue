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
