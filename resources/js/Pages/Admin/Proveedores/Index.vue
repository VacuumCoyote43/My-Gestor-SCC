<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    proveedores: Object,
    filters: Object,
});

const columns = [
    { key: 'nombre_legal', label: 'Nombre Legal', sortable: true },
    { key: 'nif_cif', label: 'NIF/CIF', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'es_liga', label: 'Tipo', sortable: false },
    { key: 'actions', label: 'Acciones', sortable: false, class: 'text-right', rowClass: 'text-right' },
];
</script>

<template>
    <Head title="Gestión de Proveedores" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight">
                    Gestión de Proveedores
                </h2>
                <Link :href="route('admin.proveedores.create')" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-primary-500/50 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-500/50 focus:outline-none focus:ring-4 focus:ring-primary-300 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Proveedor
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Buscador -->
                <SearchFilter
                    :filters="filters"
                    route-name="admin.proveedores.index"
                    placeholder="Buscar proveedores por nombre o NIF/CIF..."
                />

                <DataTable :rows="proveedores" :columns="columns" :filters="filters" route-name="admin.proveedores.index">
                    <template #cell-nombre_legal="{ row }">
                        <span class="font-semibold text-gray-900">{{ row.nombre_legal }}</span>
                    </template>
                    <template #cell-nif_cif="{ row }">
                        <span class="text-gray-500">{{ row.nif_cif }}</span>
                    </template>
                    <template #cell-email="{ row }">
                        <span class="text-gray-500">{{ row.email }}</span>
                    </template>
                    <template #cell-es_liga="{ row }">
                        <span v-if="row.es_liga" class="inline-flex rounded-full bg-purple-100 px-2 text-xs font-semibold leading-5 text-purple-800">
                            Liga
                        </span>
                        <span v-else class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">
                            Proveedor
                        </span>
                    </template>
                    <template #cell-actions="{ row }">
                        <Link :href="route('admin.proveedores.edit', row.id)" class="mr-3 text-sm font-medium text-blue-600 transition hover:text-blue-900">
                            Editar
                        </Link>
                        <Link :href="route('admin.proveedores.show', row.id)" class="text-sm font-medium text-green-600 transition hover:text-green-900">
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
