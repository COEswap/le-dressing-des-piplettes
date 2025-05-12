import './bootstrap';
import { createApp } from 'vue';
import { createStore } from 'vuex';
import { createRouter, createWebHistory } from 'vue-router';
import PrimeVue from 'primevue/config';
import App from './components/App.vue';
import axios from 'axios';


// Import routes
import routes from './routes';

// Create router instance
const router = createRouter({
    history: createWebHistory(),
    routes
});

// Create Vuex store
const store = createStore({
    state() {
        return {
            // Initial state
            cart: [],
            user: JSON.parse(localStorage.getItem('user') || 'null'),
            isAuthenticated: !!localStorage.getItem('token')
        }
    },
    mutations: {
        // User mutations
        setUser(state, user) {
            state.user = user;
            state.isAuthenticated = !!user;
            
            if (user) {
                localStorage.setItem('user', JSON.stringify(user));
            } else {
                localStorage.removeItem('user');
            }
        },
        setToken(state, token) {
            if (token) {
                localStorage.setItem('token', token);
            } else {
                localStorage.removeItem('token');
            }
        },
        
        // Cart mutations
        addToCart(state, item) {
            const existingItem = state.cart.find(
                i => i.product.id === item.product.id && i.size.id === item.size.id
            );
            
            if (existingItem) {
                existingItem.quantity += item.quantity;
            } else {
                state.cart.push(item);
            }
        },
        removeFromCart(state, index) {
            state.cart.splice(index, 1);
        },
        updateCartItem(state, { index, quantity }) {
            state.cart[index].quantity = quantity;
        },
        clearCart(state) {
            state.cart = [];
        }
    },
    actions: {
        // Authentication actions
        async login({ commit }, credentials) {
            const response = await axios.post('/api/login', credentials);
            const { user, token } = response.data;
            
            commit('setUser', user);
            commit('setToken', token);
            
            // Configure axios with token
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            
            return user;
        },
        async register({ commit }, userData) {
            const response = await axios.post('/api/register', userData);
            const { user, token } = response.data;
            
            commit('setUser', user);
            commit('setToken', token);
            
            // Configure axios with token
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            
            return user;
        },
        async logout({ commit }) {
            try {
                await axios.post('/api/logout');
            } catch (error) {
                console.error('Erreur lors de la déconnexion:', error);
            }
            
            commit('setUser', null);
            commit('setToken', null);
            
            // Remove axios authorization header
            delete axios.defaults.headers.common['Authorization'];
        },
        
        // Cart actions
        checkout({ state, commit }) {
            // This would typically call an API to create an order
            console.log('Checkout with cart items:', state.cart);
            commit('clearCart');
        }
    },
    getters: {
        isAuthenticated: state => state.isAuthenticated,
        user: state => state.user,
        cart: state => state.cart,
        cartTotal: state => {
            return state.cart.reduce((total, item) => {
                return total + (item.product.price * item.quantity);
            }, 0);
        },
        cartItemCount: state => {
            return state.cart.reduce((count, item) => {
                return count + item.quantity;
            }, 0);
        }
    }
});

// Configure axios
// Set the base URL to your API
axios.defaults.baseURL = '/';

// Add JWT token to headers if it exists
const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Add interceptor for 401 responses (unauthorized)
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            // If we get a 401, clear auth data and redirect to login
            store.commit('setUser', null);
            store.commit('setToken', null);
            router.push('/login');
        }
        return Promise.reject(error);
    }
);

// Create Vue application
const app = createApp(App);

// Use router and store
app.use(router);
app.use(store);
app.use(PrimeVue);

// Mount the app
app.mount('#app');
