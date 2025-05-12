<template>
<div class="admin-categories">
<div class="flex justify-between items-center mb-6">
<h2 class="text-2xl font-semibold text-gray-800">Gestion des catégories</h2>
<button
@click="showAddModal"
class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
>
Ajouter une catégorie
</button>
</div>

<!-- Liste des catégories -->
<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200">
<thead class="bg-gray-50">
<tr>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Nom
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
Description
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Nombre de produits
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
<tr v-for="category in categories" :key="category.id">
<td class="px-6 py-4 whitespace-nowrap">
<div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
</td>
<td class="px-6 py-4 hidden sm:table-cell">
<div class="text-sm text-gray-500">{{ category.description || '-' }}</div>
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
{{ category.products_count || 0 }}
</td>
<td class="px-6 py-4 whitespace-nowrap">
<span
:class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
>
{{ category.is_active ? 'Actif' : 'Inactif' }}
</span>
</td>
<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
<div class="flex items-center justify-end space-x-2">
<button
@click="editCategory(category)"
class="text-indigo-600 hover:text-indigo-900"
title="Modifier"
>
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
</svg>
</button>
<button
@click="deleteCategory(category)"
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
</div>

<!-- Modal d'ajout/édition -->
<div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto p-4">
<div class="flex items-center justify-center min-h-screen">
<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
<div class="relative bg-white rounded-lg max-w-lg w-full mx-auto">
<form @submit.prevent="saveCategory">
<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
<h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
{{ editingCategory.id ? 'Modifier la catégorie' : 'Ajouter une catégorie' }}
</h3>
<div class="space-y-4">
<div>
<label for="category-name" class="block text-sm font-medium text-gray-700">Nom</label>
<input
id="category-name"
v-model="editingCategory.name"
type="text"
required
class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
>
</div>
<div>
<label for="category-description" class="block text-sm font-medium text-gray-700">Description</label>
<textarea
id="category-description"
v-model="editingCategory.description"
rows="3"
class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
></textarea>
</div>
<div class="flex items-center">
<input
id="category-active"
v-model="editingCategory.is_active"
type="checkbox"
class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
>
<label for="category-active" class="ml-2 block text-sm text-gray-900">
Catégorie active
</label>
</div>
</div>
</div>
<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
<button
type="submit"
:disabled="saving"
class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm"
>
{{ saving ? 'Enregistrement...' : 'Enregistrer' }}
</button>
<button
type="button"
@click="closeModal"
class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
>
Annuler
</button>
</div>
</form>
</div>
</div>
</div>
</div>
</template>

<script>
import axios from 'axios';

export default {
name: 'AdminCategories',
data() {
return {
categories: [],
showModal: false,
editingCategory: {
id: null,
name: '',
description: '',
is_active: true
},
saving: false
};
},
created() {
this.loadCategories();
},
methods: {
async loadCategories() {
try {
const response = await axios.get('/api/admin/categories');
this.categories = response.data;
} catch (error) {
console.error('Erreur lors du chargement des catégories:', error);
alert('Impossible de charger les catégories');
}
},
showAddModal() {
this.editingCategory = {
id: null,
name: '',
description: '',
is_active: true
};
this.showModal = true;
},
editCategory(category) {
this.editingCategory = { ...category };
this.showModal = true;
},
closeModal() {
this.showModal = false;
this.editingCategory = {
id: null,
name: '',
description: '',
is_active: true
};
},
async saveCategory() {
this.saving = true;
try {
if (this.editingCategory.id) {
await axios.put(`/api/admin/categories/${this.editingCategory.id}`, this.editingCategory);
alert('Catégorie mise à jour avec succès');
} else {
await axios.post('/api/admin/categories', this.editingCategory);
alert('Catégorie créée avec succès');
}
this.closeModal();
this.loadCategories();
} catch (error) {
console.error('Erreur lors de l\'enregistrement de la catégorie:', error);
alert('Impossible d\'enregistrer la catégorie');
} finally {
this.saving = false;
}
},
async deleteCategory(category) {
if (!confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${category.name}" ?`)) {
return;
}
try {
await axios.delete(`/api/admin/categories/${category.id}`);
this.loadCategories();
alert('Catégorie supprimée avec succès');
} catch (error) {
console.error('Erreur lors de la suppression de la catégorie:', error);
if (error.response && error.response.status === 422) {
alert('Impossible de supprimer cette catégorie car elle contient des produits.');
} else {
alert('Impossible de supprimer la catégorie');
}
}
}
}
};
</script>
