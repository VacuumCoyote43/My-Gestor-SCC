<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    clubes: Object,
    filters: Object,
});

const columns = [
    { key: 'nombre', label: 'Nombre', sortable: true },
    { key: 'cif', label: 'CIF', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'created_at', label: 'Creado', sortable: true },
    { key: 'actions', label: 'Acciones', sortable: false, class: 'text-right', rowClass: 'text-right' },
];
</script>

<template>
    <Head title="Gestión de Clubes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight">
                    Gestión de Clubes
                </h2>
                <Link :href="route('admin.clubes.create')" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-primary-500/50 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-500/50 focus:outline-none focus:ring-4 focus:ring-primary-300 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Club
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Buscador -->
                <SearchFilter
                    :filters="filters"
                    route-name="admin.clubes.index"
                    placeholder="Buscar clubes por nombre o CIF..."
                />

                <DataTable :rows="clubes" :columns="columns" :filters="filters" route-name="admin.clubes.index">
                    <template #cell-nombre="{ row }">
                        <span class="font-semibold text-gray-900">{{ row.nombre }}</span>
                    </template>
                    <template #cell-cif="{ row }">
                        <span class="text-gray-500">{{ row.cif || '-' }}</span>
                    </template>
                    <template #cell-email="{ row }">
                        <span class="text-gray-500">{{ row.email || '-' }}</span>
                    </template>
                    <template #cell-created_at="{ row }">
                        <span class="text-gray-500">{{ new Date(row.created_at).toLocaleDateString('es-ES') }}</span>
                    </template>
                    <template #cell-actions="{ row }">
                        <Link :href="route('admin.clubes.edit', row.id)" class="mr-3 text-sm font-medium text-blue-600 transition hover:text-blue-900">
                            Editar
                        </Link>
                        <Link :href="route('admin.clubes.show', row.id)" class="text-sm font-medium text-green-600 transition hover:text-green-900">
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
