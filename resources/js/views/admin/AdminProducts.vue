<template>
<div class="admin-products">
<div class="flex justify-between items-center mb-6">
<h2 class="text-2xl font-semibold text-gray-800">Gestion des produits</h2>
<router-link
to="/admin/products/new"
class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
>
Ajouter un produit
</router-link>
</div>

<!-- Filtres -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
<input
v-model="filters.search"
type="text"
placeholder="Nom, référence..."
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@input="debounceSearch"
>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
<select
v-model="filters.category_id"
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@change="loadProducts"
>
<option value="">Toutes les catégories</option>
<option v-for="category in categories" :key="category.id" :value="category.id">
{{ category.name }}
</option>
</select>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
<select
v-model="filters.is_active"
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@change="loadProducts"
>
<option value="">Tous les statuts</option>
<option value="true">Actif</option>
<option value="false">Inactif</option>
</select>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
<select
v-model="filters.stock_status"
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@change="loadProducts"
>
<option value="">Tous</option>
<option value="low">Stock faible</option>
<option value="out">Rupture de stock</option>
</select>
</div>
</div>
</div>

<!-- Liste des produits -->
<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200">
<thead class="bg-gray-50">
<tr>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Produit
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Référence
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
Catégorie
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Prix
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
Stock
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Statut
</th>
<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
Actions
</th>
</tr>
</thead>
<tbody class="bg-white divide-y divide-gray-200">
<tr v-for="product in products" :key="product.id">
<td class="px-6 py-4 whitespace-nowrap">
<div class="flex items-center">
<div class="h-10 w-10 flex-shrink-0">
<img class="h-10 w-10 rounded-full object-cover" :src="product.image_url" :alt="product.name">
</div>
<div class="ml-4">
<div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
</div>
</div>
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
{{ product.reference }}
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
{{ product.category ? product.category.name : '-' }}
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
{{ formatCurrency(product.price) }}
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
<div v-if="product.sizes && product.sizes.length > 0">
<span v-for="size in product.sizes" :key="size.id" class="inline-block mr-2">
{{ size.name }}: {{ size.pivot.stock }}
</span>
</div>
<span v-else>-</span>
</td>
<td class="px-6 py-4 whitespace-nowrap">
<span :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
{{ product.is_active ? 'Actif' : 'Inactif' }}
</span>
</td>
<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
<div class="flex items-center justify-end space-x-2">
<router-link
:to="`/admin/products/${product.id}/edit`"
class="text-indigo-600 hover:text-indigo-900"
title="Modifier"
>
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
</svg>
</router-link>
<button
@click="deleteProduct(product)"
class="text-red-600 hover:text-red-900"
title="Supprimer"
>
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
</svg>
</button>
</div>
</td>
</tr>
</tbody>
</table>
<!-- Pagination -->
<div v-if="pagination.total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
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
<span class="font-medium">{{ pagination.from }}</span>
à
<span class="font-medium">{{ pagination.to }}</span>
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
Précédent
</button>
<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
Page {{ pagination.current_page }} sur {{ pagination.last_page }}
</span>
<button
@click="changePage(pagination.current_page + 1)"
:disabled="pagination.current_page === pagination.last_page"
class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
>
Suivant
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

export default {
name: 'AdminProducts',
data() {
return {
products: [],
categories: [],
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
category_id: '',
is_active: '',
stock_status: ''
},
loading: false,
error: null,
searchTimeout: null
};
},
created() {
this.loadCategories();
this.loadProducts();
},
methods: {
async loadCategories() {
try {
const response = await axios.get('/api/admin/categories');
this.categories = response.data;
} catch (error) {
console.error('Erreur lors du chargement des catégories:', error);
}
},
async loadProducts(page = 1) {
this.loading = true;
this.error = null;
try {
const params = {
page,
...this.filters
};
const response = await axios.get('/api/admin/products', { params });
this.products = response.data.data || [];
this.pagination = {
current_page: response.data.current_page || 1,
last_page: response.data.last_page || 1,
per_page: response.data.per_page || 10,
total: response.data.total || 0,
from: response.data.from || 0,
to: response.data.to || 0
};
} catch (error) {
console.error('Erreur lors du chargement des produits:', error);
this.error = error.response?.data?.message || 'Impossible de charger les produits';
} finally {
this.loading = false;
}
},
debounceSearch() {
clearTimeout(this.searchTimeout);
this.searchTimeout = setTimeout(() => {
this.loadProducts(1);
}, 500);
},
changePage(page) {
if (page >= 1 && page <= this.pagination.last_page) {
this.loadProducts(page);
}
},
formatCurrency(amount) {
return new Intl.NumberFormat('fr-FR', {
style: 'currency',
currency: 'EUR'
}).format(amount);
},
async deleteProduct(product) {
if (!confirm(`Êtes-vous sûr de vouloir supprimer le produit "${product.name}" ?`)) {
return;
}
try {
await axios.delete(`/api/admin/products/${product.id}`);
this.loadProducts(this.pagination.current_page);
alert('Produit supprimé avec succès');
} catch (error) {
console.error('Erreur lors de la suppression du produit:', error);
alert('Impossible de supprimer le produit');
}
}
}
};
</script>
