<template>
  <div class="app min-h-screen bg-gray-50">
    <AppHeader />
    <main>
      <router-view></router-view>
    </main>
    <footer class="bg-gray-800 text-white py-8 px-4">
      <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 class="text-xl font-semibold mb-4">Le dressing des piplettes</h3>
            <p class="mb-4">Votre boutique de vêtements en ligne avec des micro-collections uniques.</p>
            <div class="flex space-x-4">
              <a href="#" class="text-white hover:text-purple-300 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2.04C6.5 2.04 2 6.53 2 12.06C2 17.06 5.66 21.21 10.44 21.96V14.96H7.9V12.06H10.44V9.85C10.44 7.34 11.93 5.96 14.22 5.96C15.31 5.96 16.45 6.15 16.45 6.15V8.62H15.19C13.95 8.62 13.56 9.39 13.56 10.18V12.06H16.34L15.89 14.96H13.56V21.96C18.34 21.21 22 17.06 22 12.06C22 6.53 17.5 2.04 12 2.04Z" />
                </svg>
              </a>
              <a href="#" class="text-white hover:text-purple-300 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 9.52C10.61 9.52 9.52 10.61 9.52 12C9.52 13.39 10.61 14.48 12 14.48C13.39 14.48 14.48 13.39 14.48 12C14.48 10.61 13.39 9.52 12 9.52ZM12 2H12C10.27 2 8.57 2.01 6.86 2.05C5.52 2.08 4.31 2.32 3.24 3.39C1.44 5.19 1.96 7.71 1.96 12C1.96 14.44 1.76 17.67 3.23 19.78C5.13 22.45 8.28 22 12.02 22C16.07 22 17.87 22.01 19.39 20.49C21.14 18.74 20.96 16.31 20.96 12C20.96 10.04 21.05 8.1 20.57 6.17C20.16 4.56 18.94 3.33 17.32 2.92C16.39 2.72 15.41 2.62 14.42 2.58C13.04 2.52 12.52 2 12 2ZM12 7.58C14.33 7.58 16.23 9.48 16.23 11.81C16.23 14.14 14.33 16.04 12 16.04C9.67 16.04 7.77 14.14 7.77 11.81C7.77 9.48 9.68 7.58 12 7.58ZM17.89 5.5C17.89 6.23 17.3 6.82 16.57 6.82C15.84 6.82 15.25 6.23 15.25 5.5C15.25 4.77 15.84 4.18 16.57 4.18C17.3 4.18 17.89 4.77 17.89 5.5Z" />
                </svg>
              </a>
            </div>
          </div>
          
          <div>
            <h3 class="text-xl font-semibold mb-4">Liens rapides</h3>
            <ul class="space-y-2">
              <li><router-link to="/" class="hover:text-purple-300 transition-colors">Accueil</router-link></li>
              <li><router-link to="/products" class="hover:text-purple-300 transition-colors">Collections</router-link></li>
              <li><a href="#" class="hover:text-purple-300 transition-colors">À propos</a></li>
              <li><a href="#" class="hover:text-purple-300 transition-colors">Contact</a></li>
            </ul>
          </div>
          
          <div>
            <h3 class="text-xl font-semibold mb-4">Contact</h3>
            <address class="not-italic">
              <p class="mb-2">123 Rue de la Mode</p>
              <p class="mb-2">75000 Paris, France</p>
              <p class="mb-2">Téléphone: 01 23 45 67 89</p>
              <p class="mb-2">Email: contact@dressing-piplettes.fr</p>
            </address>
          </div>
        </div>
        
        <div class="border-t border-gray-700 mt-8 pt-8 text-center">
          <p>&copy; {{ new Date().getFullYear() }} Le dressing des piplettes. Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import AppHeader from './AppHeader.vue';

export default {
  name: 'App',
  components: {
    AppHeader
  }
}
</script>
