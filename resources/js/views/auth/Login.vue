<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Connexion à votre compte
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Ou
        <router-link to="/register" class="font-medium text-purple-600 hover:text-purple-500">
          créez un compte si vous n'en avez pas
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="login" class="space-y-6">
          <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-700">{{ errorMessage }}</p>
              </div>
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse e-mail
            </label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" required v-model="email"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input id="password" name="password" type="password" autocomplete="current-password" required v-model="password"
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember-me" name="remember-me" type="checkbox" v-model="rememberMe"
                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Se souvenir de moi
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-purple-600 hover:text-purple-500">
                Mot de passe oublié ?
              </a>
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              :class="{ 'opacity-75 cursor-not-allowed': loading }"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Se connecter
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Ou continuer avec
              </span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-3 gap-3">
            <div>
              <a @click.prevent="socialLogin('facebook')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('google')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 12.25C6 11.57 6.13 10.9 6.37 10.28L2.58 7.5C1.89 8.91 1.5 10.47 1.5 12.12C1.5 13.8 1.93 15.41 2.69 16.85L6.39 13.92C6.14 13.4 6 12.85 6 12.25Z" fill="#FBBC05" />
                  <path d="M12.25 6.25C13.84 6.25 15.29 6.88 16.38 7.92L19.7 4.6C17.64 2.69 15.04 1.5 12.25 1.5C7.87 1.5 4.08 4.24 2.58 8.26L6.38 11.04C7.1 8.32 9.47 6.25 12.25 6.25Z" fill="#EA4335" />
                  <path d="M12.25 18.25C9.47 18.25 7.1 16.18 6.37 13.46L2.58 16.23C4.08 20.26 7.87 23 12.25 23C14.93 23 17.62 21.9 19.69 19.99L16.14 17.26C15.14 17.91 13.76 18.25 12.25 18.25Z" fill="#34A853" />
                  <path d="M22.5 12.25C22.5 11.5 22.39 10.69 22.25 10H12.25V14.25H18C17.75 15.75 16.83 16.9 15.53 17.64L19.08 20.37C21.14 18.35 22.5 15.58 22.5 12.25Z" fill="#4285F4" />
                </svg>
              </a>
            </div>

            <div>
              <a @click.prevent="socialLogin('apple')" href="#"
                class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M12.7 10c0-2.8 2.3-4.1 2.3-4.1s-1.3-1.9-3.4-1.9c-1.5 0-2.5.8-3.3.8-.9 0-1.7-.7-3-.7-2.2 0-4.4 1.7-4.4 5.2 0 2.1.8 4.3 1.7 5.7.8 1.2 1.5 2.1 2.7 2.1 1 0 1.4-.6 2.9-.6s1.8.6 2.9.6c1.2 0 2-1.1 2.7-2.1.6-.9.9-1.7 1.1-2.4-1.3-.5-2.2-2-2.2-3.6zM11 5.1c.7-.8 1.8-1.3 2.7-1.4-2.3.4-3.5 2-3.5 2 .4-.1 1.2-.3 2-.3.4-.1.8-.1.8-.3z" />
                </svg>
              </a>
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
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      rememberMe: false,
      loading: false,
      errorMessage: ''
    };
  },
  methods: {
    async login() {
      this.loading = true;
      this.errorMessage = '';
      
      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password
        });
        
        // Stocker le token et les informations utilisateur dans le localStorage
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        // Configurer axios pour inclure le token dans les requêtes futures
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // Émettre un événement personnalisé pour notifier les autres composants
        window.dispatchEvent(new CustomEvent('auth-changed'));
        
        // Rediriger vers la page d'accueil
        this.$router.push('/');
      } catch (error) {
        console.error('Erreur de connexion:', error);
        this.errorMessage = error.response?.data?.message || 'Une erreur est survenue lors de la connexion.';
      } finally {
        this.loading = false;
      }
    },
    async socialLogin(provider) {
      try {
        // Récupérer l'URL de redirection
        const response = await axios.get(`/api/auth/${provider}`);
        
        // Ouvrir une fenêtre popup pour le processus d'authentification
        const width = 600;
        const height = 600;
        const left = (window.innerWidth / 2) - (width / 2);
        const top = (window.innerHeight / 2) - (height / 2);
        
        const popup = window.open(
          response.data.url,
          `${provider}Auth`,
          `width=${width},height=${height},left=${left},top=${top}`
        );
        
        // Fonction pour vérifier si la popup est fermée
        const checkPopup = setInterval(() => {
          if (popup.closed) {
            clearInterval(checkPopup);
            // Vérifier si l'authentification a réussi en vérifiant le localStorage
            if (localStorage.getItem('token')) {
              // Émettre l'événement auth-changed
              window.dispatchEvent(new CustomEvent('auth-changed'));
              this.$router.push('/');
            }
          }
        }, 500);
        
      } catch (error) {
        console.error(`Erreur lors de la connexion avec ${provider}:`, error);
        this.errorMessage = `Une erreur est survenue lors de la connexion avec ${provider}.`;
      }
    }
  }
};
</script>
