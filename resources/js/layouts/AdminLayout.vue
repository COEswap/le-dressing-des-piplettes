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
