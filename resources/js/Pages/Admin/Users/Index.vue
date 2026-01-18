<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import Card from '@/Components/Card.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: Object,
    filters: Object,
});

const roleFilter = ref(props.filters.role || '');
const activeFilter = ref(props.filters.active || '');

const loginAs = (userId) => {
    if (confirm('¿Estás seguro de que quieres iniciar sesión como este usuario?')) {
        router.post(route('admin.users.login-as', userId));
    }
};

const applyFilters = () => {
    router.get(route('admin.users.index'), {
        search: props.filters.search,
        role: roleFilter.value,
        active: activeFilter.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Gestión de Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Gestión de Usuarios
                </h2>
                <Link :href="route('admin.users.create')" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-primary-500/50 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-500/50 focus:outline-none focus:ring-4 focus:ring-primary-300 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Usuario
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Buscador -->
                <SearchFilter
                    :filters="filters"
                    route-name="admin.users.index"
                    placeholder="Buscar usuarios por nombre o email..."
                    :show-filters="true"
                >
                    <template #filters>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                                <select
                                    v-model="roleFilter"
                                    @change="applyFilters"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="">Todos los roles</option>
                                    <option value="admin">Administrador</option>
                                    <option value="proveedor">Proveedor</option>
                                    <option value="gestor_club">Gestor Club</option>
                                    <option value="jugador">Jugador</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <select
                                    v-model="activeFilter"
                                    @change="applyFilters"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="">Todos</option>
                                    <option value="true">Activos</option>
                                    <option value="false">Inactivos</option>
                                </select>
                            </div>
                        </div>
                    </template>
                </SearchFilter>

                <Card :padding="false">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nombre</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Rol</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Estado</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            {{ user.name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ user.email }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            <span v-if="user.role" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ user.role.nombre }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="user.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ user.active ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                                            <button @click="loginAs(user.id)" class="text-purple-600 hover:text-purple-900 mr-3" title="Iniciar sesión como este usuario">
                                                🔐 Login
                                            </button>
                                            <Link :href="route('admin.users.edit', user.id)" class="text-blue-600 hover:text-blue-900 mr-3">
                                                Editar
                                            </Link>
                                            <Link :href="route('admin.users.show', user.id)" class="text-green-600 hover:text-green-900">
                                                Ver
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div v-if="users.links" class="flex justify-between items-center mt-4">
                            <div class="text-sm text-gray-700">
                                Mostrando {{ users.from }} a {{ users.to }} de {{ users.total }} resultados
                            </div>
                            <div class="flex gap-1">
                                <component v-for="link in users.links" :key="link.label" :is="link.url ? Link : 'span'" :href="link.url" :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-3 py-2 text-sm border rounded" v-html="link.label"></component>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
