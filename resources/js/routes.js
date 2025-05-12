import Home from './views/Home.vue';
import ProductList from './views/ProductList.vue';
import ProductDetail from './views/ProductDetail.vue';
import Login from './views/auth/Login.vue';
import Register from './views/auth/Register.vue';
import Profile from './views/auth/Profile.vue';

// Admin components
import AdminLayout from './layouts/AdminLayout.vue';
import AdminDashboard from './views/admin/AdminDashboard.vue';
import AdminProducts from './views/admin/AdminProducts.vue';
import AdminProductEdit from './views/admin/AdminProductEdit.vue';
import AdminOrders from './views/admin/AdminOrders.vue';
import AdminOrderDetails from './views/admin/AdminOrderDetails.vue';
import AdminUsers from './views/admin/AdminUsers.vue';
import AdminCategories from './views/admin/AdminCategories.vue';

// Fonction pour vérifier si l'utilisateur est authentifié
const requireAuth = (to, from, next) => {
  if (!localStorage.getItem('token')) {
    next('/login');
  } else {
    next();
  }
};

// Fonction pour vérifier si l'utilisateur est admin
const requireAdmin = (to, from, next) => {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  if (!localStorage.getItem('token') || !user.is_admin) {
    next('/');
  } else {
    next();
  }
};

// Fonction pour rediriger les utilisateurs déjà connectés
const redirectIfAuthenticated = (to, from, next) => {
  if (localStorage.getItem('token')) {
    next('/');
  } else {
    next();
  }
};

const routes = [
  { 
    path: '/', 
    component: Home, 
    name: 'home' 
  },
  { 
    path: '/products', 
    component: ProductList, 
    name: 'products' 
  },
  { 
    path: '/products/:id', 
    component: ProductDetail, 
    name: 'product-detail' 
  },
  {
    path: '/login',
    component: Login,
    name: 'login',
    beforeEnter: redirectIfAuthenticated
  },
  {
    path: '/register',
    component: Register,
    name: 'register',
    beforeEnter: redirectIfAuthenticated
  },
  {
    path: '/profile',
    component: Profile,
    name: 'profile',
    beforeEnter: requireAuth
  },
  // Routes d'administration
  {
    path: '/admin',
    component: AdminLayout,
    beforeEnter: requireAdmin,
    children: [
      {
        path: '',
        redirect: '/admin/dashboard'
      },
      {
        path: 'dashboard',
        component: AdminDashboard,
        name: 'admin-dashboard'
      },
      {
        path: 'products',
        component: AdminProducts,
        name: 'admin-products'
      },
      {
        path: 'products/new',
        component: AdminProductEdit,
        name: 'admin-product-new'
      },
      {
        path: 'products/:id/edit',
        component: AdminProductEdit,
        name: 'admin-product-edit'
      },
      {
        path: 'orders',
        component: AdminOrders,
        name: 'admin-orders'
      },
      {
        path: 'orders/:id',
        component: AdminOrderDetails,
        name: 'admin-order-details'
      },
      {
        path: 'users',
        component: AdminUsers,
        name: 'admin-users'
      },
      {
        path: 'categories',
        component: AdminCategories,
        name: 'admin-categories'
      }
    ]
  }
];

export default routes;
