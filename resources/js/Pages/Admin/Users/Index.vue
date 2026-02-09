<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: Object,
    filters: Object,
});

const roleFilter = ref(props.filters.role || '');
const activeFilter = ref(props.filters.active || '');

const columns = [
    { key: 'name', label: 'Nombre', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'role', label: 'Rol', sortable: false },
    { key: 'active', label: 'Estado', sortable: false },
    { key: 'actions', label: 'Acciones', sortable: false, class: 'text-right', rowClass: 'text-right' },
];

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
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight">
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

                <DataTable :rows="users" :columns="columns" :filters="filters" route-name="admin.users.index">
                    <template #cell-name="{ row }">
                        <span class="font-semibold text-gray-900">{{ row.name }}</span>
                    </template>
                    <template #cell-email="{ row }">
                        <span class="text-gray-500">{{ row.email }}</span>
                    </template>
                    <template #cell-role="{ row }">
                        <span v-if="row.role" class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">
                            {{ row.role.nombre }}
                        </span>
                    </template>
                    <template #cell-active="{ row }">
                        <span :class="row.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                            {{ row.active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </template>
                    <template #cell-actions="{ row }">
                        <button @click="loginAs(row.id)" class="mr-3 text-sm font-medium text-purple-600 transition hover:text-purple-900" title="Iniciar sesión como este usuario">
                            🔐 Login
                        </button>
                        <Link :href="route('admin.users.edit', row.id)" class="mr-3 text-sm font-medium text-blue-600 transition hover:text-blue-900">
                            Editar
                        </Link>
                        <Link :href="route('admin.users.show', row.id)" class="text-sm font-medium text-green-600 transition hover:text-green-900">
                            Ver
                        </Link>
                    </template>
                    <template #pagination="{ links }">
                        <Pagination :links="links" />
                    </template>
                </DataTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
