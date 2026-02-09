<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    incidencias: Object,
    filters: Object,
});

const estadoFilter = ref(props.filters.estado || '');
const prioridadFilter = ref(props.filters.prioridad || '');
const categoriaFilter = ref(props.filters.categoria || '');

const columns = [
    { key: 'asunto', label: 'Asunto', sortable: true },
    { key: 'categoria', label: 'Categoría', sortable: true },
    { key: 'prioridad', label: 'Prioridad', sortable: true },
    { key: 'estado', label: 'Estado', sortable: true },
    { key: 'fecha_apertura', label: 'Fecha', sortable: true },
    { key: 'actions', label: 'Acciones', sortable: false, class: 'text-right', rowClass: 'text-right' },
];

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'abierta': 'bg-red-100 text-red-800',
        'en_progreso': 'bg-yellow-100 text-yellow-800',
        'cerrada': 'bg-green-100 text-green-800',
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};

const getPrioridadBadgeClass = (prioridad) => {
    const classes = {
        'baja': 'bg-gray-100 text-gray-800',
        'media': 'bg-blue-100 text-blue-800',
        'alta': 'bg-orange-100 text-orange-800',
        'urgente': 'bg-red-100 text-red-800',
    };
    return classes[prioridad] || 'bg-gray-100 text-gray-800';
};

const getEstadoLabel = (estado) => {
    const labels = {
        'abierta': 'Abierta',
        'en_progreso': 'En Progreso',
        'cerrada': 'Cerrada',
    };
    return labels[estado] || estado;
};

const getPrioridadLabel = (prioridad) => {
    const labels = {
        'baja': 'Baja',
        'media': 'Media',
        'alta': 'Alta',
        'urgente': 'Urgente',
    };
    return labels[prioridad] || prioridad;
};

const applyFilters = () => {
    router.get(route('incidencias.index'), {
        search: props.filters.search,
        estado: estadoFilter.value,
        prioridad: prioridadFilter.value,
        categoria: categoriaFilter.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Incidencias" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Incidencias</h2>
                    <p class="mt-1 text-sm text-gray-500">Gestión de incidencias y soporte</p>
                </div>
                <Link :href="route('incidencias.create')" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-primary-500/50 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-500/50 focus:outline-none focus:ring-4 focus:ring-primary-300 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Incidencia
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Buscador y Filtros -->
                <SearchFilter
                    :filters="filters"
                    route-name="incidencias.index"
                    placeholder="Buscar incidencias por asunto..."
                    :show-filters="true"
                >
                    <template #filters>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <select
                                    v-model="estadoFilter"
                                    @change="applyFilters"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="">Todos los estados</option>
                                    <option value="abierta">Abierta</option>
                                    <option value="en_progreso">En Progreso</option>
                                    <option value="cerrada">Cerrada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Prioridad</label>
                                <select
                                    v-model="prioridadFilter"
                                    @change="applyFilters"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="">Todas las prioridades</option>
                                    <option value="baja">Baja</option>
                                    <option value="media">Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                                <select
                                    v-model="categoriaFilter"
                                    @change="applyFilters"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="">Todas las categorías</option>
                                    <option value="tecnica">Técnica</option>
                                    <option value="administrativa">Administrativa</option>
                                    <option value="economica">Económica</option>
                                    <option value="otra">Otra</option>
                                </select>
                            </div>
                        </div>
                    </template>
                </SearchFilter>

                <DataTable :rows="incidencias" :columns="columns" :filters="filters" route-name="incidencias.index" :sticky-header="true">
                    <template #cell-asunto="{ row }">
                        <div class="flex items-center gap-3">
                            <span class="h-2 w-2 rounded-full" :class="{
                                'bg-red-500': row.estado === 'abierta',
                                'bg-yellow-500': row.estado === 'en_progreso',
                                'bg-green-500': row.estado === 'cerrada'
                            }"></span>
                            <span class="font-medium text-gray-900">{{ row.asunto }}</span>
                        </div>
                    </template>
                    <template #cell-categoria="{ row }">
                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 capitalize">
                            {{ row.categoria }}
                        </span>
                    </template>
                    <template #cell-prioridad="{ row }">
                        <span :class="getPrioridadBadgeClass(row.prioridad)" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize">
                            {{ getPrioridadLabel(row.prioridad) }}
                        </span>
                    </template>
                    <template #cell-estado="{ row }">
                        <span :class="getEstadoBadgeClass(row.estado)" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold capitalize">
                            {{ getEstadoLabel(row.estado) }}
                        </span>
                    </template>
                    <template #cell-fecha_apertura="{ row }">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="mr-2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ new Date(row.fecha_apertura).toLocaleDateString('es-ES') }}
                        </div>
                    </template>
                    <template #cell-actions="{ row }">
                        <Link :href="route('incidencias.show', row.id)" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-primary-600 transition hover:bg-primary-50 hover:text-primary-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
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
