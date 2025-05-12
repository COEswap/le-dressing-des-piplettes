<template>
<div class="admin-users">
<div class="flex justify-between items-center mb-6">
<h2 class="text-2xl font-semibold text-gray-800">Gestion des utilisateurs</h2>
<button
@click="showAddModal"
class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700"
>
Ajouter un utilisateur
</button>
</div>

<!-- Filtres -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
<input
v-model="filters.search"
type="text"
placeholder="Nom, email..."
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@input="debounceSearch"
>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
<select
v-model="filters.is_admin"
class="w-full px-3 py-2 border border-gray-300 rounded-md"
@change="loadUsers"
>
<option value="">Tous les utilisateurs</option>
<option value="true">Administrateurs</option>
<option value="false">Clients</option>
</select>
</div>
</div>
</div>

<!-- Liste des utilisateurs -->
<div class="bg-white rounded-lg shadow overflow-hidden overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200">
<thead class="bg-gray-50">
<tr>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Utilisateur
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
Type
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
Email vérifié
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
Date d'inscription
</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
Commandes
</th>
<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
Actions
</th>
</tr>
</thead>
<tbody class="bg-white divide-y divide-gray-200">
<tr v-for="user in users" :key="user.id">
<td class="px-6 py-4 whitespace-nowrap">
<div class="flex items-center">
<div>
<div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
<div class="text-sm text-gray-500">{{ user.email }}</div>
</div>
</div>
</td>
<td class="px-6 py-4 whitespace-nowrap">
<span
:class="user.is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'"
class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
>
{{ user.is_admin ? 'Admin' : 'Client' }}
</span>
</td>
<td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
<span
:class="user.email_verified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
>
{{ user.email_verified ? 'Vérifié' : 'Non vérifié' }}
</span>
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
{{ formatDate(user.created_at) }}
</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
{{ user.orders_count || 0 }}
</td>
<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
<div class="flex items-center justify-end space-x-2">
<button
@click="editUser(user)"
class="text-indigo-600 hover:text-indigo-900"
title="Modifier"
>
<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
</svg>
</button>
<button
v-if="user.email !== 'admin@dressingdespiplettes.com'"
@click="deleteUser(user)"
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

<!-- Modal d'ajout/édition -->
<div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto p-4">
<div class="flex items-center justify-center min-h-screen">
<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
<div class="relative bg-white rounded-lg max-w-lg w-full mx-auto">
<form @submit.prevent="saveUser">
<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
<h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
{{ editingUser.id ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur' }}
</h3>
<div class="space-y-4">
<div>
<label for="user-name" class="block text-sm font-medium text-gray-700">Nom</label>
<input
id="user-name"
v-model="editingUser.name"
type="text"
required
class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
>
</div>
<div>
<label for="user-email" class="block text-sm font-medium text-gray-700">Email</label>
<input
id="user-email"
v-model="editingUser.email"
type="email"
required
class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
>
</div>
<div>
<label for="user-password" class="block text-sm font-medium text-gray-700">
{{ editingUser.id ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe' }}
</label>
<input
id="user-password"
v-model="editingUser.password"
type="password"
:required="!editingUser.id"
class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
>
</div>
<div class="flex items-center">
<input
id="user-is-admin"
v-model="editingUser.is_admin"
type="checkbox"
class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
>
<label for="user-is-admin" class="ml-2 block text-sm text-gray-900">
Administrateur
</label>
</div>
<div class="flex items-center">
<input
id="user-email-verified"
v-model="editingUser.email_verified"
type="checkbox"
class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
>
<label for="user-email-verified" class="ml-2 block text-sm text-gray-900">
Email vérifié
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
name: 'AdminUsers',
data() {
return {
users: [],
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
is_admin: ''
},
loading: false,
error: null,
showModal: false,
editingUser: {
id: null,
name: '',
email: '',
password: '',
is_admin: false,
email_verified: true
},
saving: false,
searchTimeout: null
};
},
created() {
this.loadUsers();
},
methods: {
async loadUsers(page = 1) {
this.loading = true;
this.error = null;
try {
const params = {
page,
...this.filters
};
const response = await axios.get('/api/admin/users', { params });
this.users = response.data.data;
this.pagination = {
current_page: response.data.current_page,
last_page: response.data.last_page,
per_page: response.data.per_page,
total: response.data.total,
from: response.data.from || 0,
to: response.data.to || 0
};
} catch (error) {
console.error('Erreur lors du chargement des utilisateurs:', error);
this.error = error.response?.data?.message || 'Impossible de charger les utilisateurs';
} finally {
this.loading = false;
}
},
debounceSearch() {
clearTimeout(this.searchTimeout);
this.searchTimeout = setTimeout(() => {
this.loadUsers(1);
}, 500);
},
changePage(page) {
if (page >= 1 && page <= this.pagination.last_page) {
this.loadUsers(page);
}
},
formatDate(dateString) {
return new Intl.DateTimeFormat('fr-FR', {
day: '2-digit',
month: '2-digit',
year: 'numeric'
}).format(new Date(dateString));
},
showAddModal() {
this.editingUser = {
id: null,
name: '',
email: '',
password: '',
is_admin: false,
email_verified: true
};
this.showModal = true;
},
editUser(user) {
this.editingUser = {
...user,
password: ''
};
this.showModal = true;
},
closeModal() {
this.showModal = false;
this.editingUser = {
id: null,
name: '',
email: '',
password: '',
is_admin: false,
email_verified: true
};
},
async saveUser() {
this.saving = true;
try {
const data = {
name: this.editingUser.name,
email: this.editingUser.email,
is_admin: this.editingUser.is_admin,
email_verified: this.editingUser.email_verified
};
if (this.editingUser.password) {
data.password = this.editingUser.password;
}

if (this.editingUser.id) {
await axios.put(`/api/admin/users/${this.editingUser.id}`, data);
alert('Utilisateur mis à jour avec succès');
} else {
await axios.post('/api/admin/users', data);
alert('Utilisateur créé avec succès');
}
this.closeModal();
this.loadUsers(this.pagination.current_page);
} catch (error) {
console.error('Erreur lors de l\'enregistrement de l\'utilisateur:', error);
alert('Impossible d\'enregistrer l\'utilisateur');
} finally {
this.saving = false;
}
},
async deleteUser(user) {
if (!confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${user.name}" ?`)) {
return;
}
try {
await axios.delete(`/api/admin/users/${user.id}`);
this.loadUsers(this.pagination.current_page);
alert('Utilisateur supprimé avec succès');
} catch (error) {
console.error('Erreur lors de la suppression de l\'utilisateur:', error);
if (error.response && error.response.status === 403) {
alert('Impossible de supprimer cet utilisateur. Il s\'agit peut-être du compte administrateur principal.');
} else {
alert('Impossible de supprimer l\'utilisateur');
}
}
}
}
};
</script>
