<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import Card from '@/Components/Card.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    incidencias: Object,
    filters: Object,
});

const estadoFilter = ref(props.filters.estado || '');
const prioridadFilter = ref(props.filters.prioridad || '');
const categoriaFilter = ref(props.filters.categoria || '');

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
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Incidencias</h2>
                    <p class="mt-1 text-primary-100">Gestión de incidencias y soporte</p>
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

                <Card :padding="false">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">Asunto</th>
                                        <th class="px-6 py-4 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">Categoría</th>
                                        <th class="px-6 py-4 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">Prioridad</th>
                                        <th class="px-6 py-4 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">Estado</th>
                                        <th class="px-6 py-4 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">Fecha</th>
                                        <th class="px-6 py-4 text-xs font-semibold tracking-wider text-right text-gray-700 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="incidencia in incidencias.data" :key="incidencia.id" class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 rounded-full mr-3" :class="{
                                                    'bg-red-500': incidencia.estado === 'abierta',
                                                    'bg-yellow-500': incidencia.estado === 'en_progreso',
                                                    'bg-green-500': incidencia.estado === 'cerrada'
                                                }"></div>
                                                {{ incidencia.asunto }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 capitalize">
                                                {{ incidencia.categoria }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getPrioridadBadgeClass(incidencia.prioridad)" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize">
                                                {{ getPrioridadLabel(incidencia.prioridad) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getEstadoBadgeClass(incidencia.estado)" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize">
                                                {{ getEstadoLabel(incidencia.estado) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ new Date(incidencia.fecha_apertura).toLocaleDateString('es-ES') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                                            <Link :href="route('incidencias.show', incidencia.id)" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-primary-600 hover:text-primary-700 hover:bg-primary-50 rounded-lg transition-colors duration-150">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Ver
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="incidencias.data.length === 0">
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="mt-4 text-sm">No se encontraron incidencias</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div v-if="incidencias.links && incidencias.links.length > 3" class="mt-6 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                            <div class="flex flex-1 justify-between sm:hidden">
                                <Link
                                    v-if="incidencias.prev_page_url"
                                    :href="incidencias.prev_page_url"
                                    class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Anterior
                                </Link>
                                <Link
                                    v-if="incidencias.next_page_url"
                                    :href="incidencias.next_page_url"
                                    class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Siguiente
                                </Link>
                            </div>
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Mostrando
                                        <span class="font-medium">{{ incidencias.from }}</span>
                                        a
                                        <span class="font-medium">{{ incidencias.to }}</span>
                                        de
                                        <span class="font-medium">{{ incidencias.total }}</span>
                                        resultados
                                    </p>
                                </div>
                                <div>
                                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                        <Link
                                            v-for="(link, index) in incidencias.links"
                                            :key="index"
                                            :href="link.url || '#'"
                                            :class="[
                                                link.active
                                                    ? 'z-10 bg-primary-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600'
                                                    : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0',
                                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold',
                                                index === 0 ? 'rounded-l-md' : '',
                                                index === incidencias.links.length - 1 ? 'rounded-r-md' : '',
                                                !link.url ? 'pointer-events-none opacity-50' : ''
                                            ]"
                                            v-html="link.label"
                                        />
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
