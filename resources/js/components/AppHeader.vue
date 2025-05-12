<template>
  <header class="bg-white shadow">
    <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
      <router-link to="/" class="text-xl font-bold text-purple-600">
        LE DRESSING DES PIPLETTES
      </router-link>
      
      <div class="hidden md:flex space-x-8">
        <router-link to="/" class="text-gray-700 hover:text-purple-600 transition-colors">
          ACCUEIL
        </router-link>
        <router-link to="/products" class="text-gray-700 hover:text-purple-600 transition-colors">
          COLLECTIONS
        </router-link>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          À PROPOS
        </a>
        <a href="#" class="text-gray-700 hover:text-purple-600 transition-colors">
          CONTACT
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
