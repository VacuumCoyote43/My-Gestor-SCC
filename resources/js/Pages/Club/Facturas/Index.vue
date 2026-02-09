<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import DataTable from '@/Components/DataTable.vue';
import Badge from '@/Components/Badge.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    facturas: Object,
    filters: Object,
    club: Object,
});

const estadoFilter = ref(props.filters.estado || '');
const mesFilter = ref(props.filters.mes || '');

const columns = [
    { key: 'numero', label: 'Número', sortable: true },
    { key: 'receptor', label: 'Receptor', sortable: false },
    { key: 'fecha_factura', label: 'Fecha', sortable: true },
    { key: 'total', label: 'Total', sortable: true, class: 'text-right', rowClass: 'text-right font-medium' },
    { key: 'estado', label: 'Estado', sortable: true, class: 'text-center', rowClass: 'text-center' },
    { key: 'actions', label: 'Acciones', sortable: false, class: 'text-right', rowClass: 'text-right' },
];

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
};

const getEstadoColor = (estado) => {
    const colors = {
        'draft': 'gray',
        'pending_payment': 'yellow',
        'payment_registered': 'blue',
        'paid': 'green',
        'cancelled': 'red',
    };
    return colors[estado] || 'gray';
};

const getEstadoLabel = (estado) => {
    const labels = {
        'draft': 'Borrador',
        'pending_payment': 'Pendiente',
        'payment_registered': 'Verificando',
        'paid': 'Pagada',
        'cancelled': 'Cancelada',
    };
    return labels[estado] || estado;
};

const applyFilters = () => {
    router.get(route('club.facturas.index'), {
        search: props.filters.search,
        estado: estadoFilter.value,
        mes: mesFilter.value,
        // Preserve sort if present
        sort: props.filters.sort,
        direction: props.filters.direction,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Facturas Emitidas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    Facturas Emitidas{{ club ? ' - ' + club.nombre : '' }}
                </h2>
                <Link :href="route('club.facturas.create')" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-primary-500/50 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-500/50 focus:outline-none focus:ring-4 focus:ring-primary-300 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Factura
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Buscador -->
                <SearchFilter
                    :filters="filters"
                    route-name="club.facturas.index"
                    placeholder="Buscar facturas por número o receptor..."
                    :show-filters="true"
                    class="mb-6"
                >
                    <template #filters>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <select
                                    v-model="estadoFilter"
                                    @change="applyFilters"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="">Todos los estados</option>
                                    <option value="draft">Borrador</option>
                                    <option value="pending_payment">Pendiente de Pago</option>
                                    <option value="payment_registered">Pago Registrado</option>
                                    <option value="paid">Pagada</option>
                                    <option value="cancelled">Cancelada</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mes</label>
                                <select
                                    v-model="mesFilter"
                                    @change="applyFilters"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="">Todos los meses</option>
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                        </div>
                    </template>
                </SearchFilter>

                <DataTable
                    :rows="facturas"
                    :columns="columns"
                    :filters="filters"
                    route-name="club.facturas.index"
                    :sticky-header="true"
                >
                    <template #cell-numero="{ row }">
                        <span class="font-medium text-gray-900">{{ row.numero || 'BORRADOR' }}</span>
                    </template>

                    <template #cell-receptor="{ row }">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs mr-3">
                                {{ (row.receptor?.nombre || row.receptor?.nombre_legal || '?').substring(0, 2).toUpperCase() }}
                            </div>
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ row.receptor?.nombre || row.receptor?.nombre_legal }}</div>
                                <div class="text-gray-500 text-xs">{{ row.receptor?.email }}</div>
                            </div>
                        </div>
                    </template>

                    <template #cell-fecha_factura="{ value }">
                        {{ new Date(value).toLocaleDateString('es-ES') }}
                    </template>

                    <template #cell-total="{ value }">
                        <span class="font-bold text-gray-900">{{ formatCurrency(value) }}</span>
                    </template>

                    <template #cell-estado="{ value }">
                        <Badge :color="getEstadoColor(value)">
                            {{ getEstadoLabel(value) }}
                        </Badge>
                    </template>

                    <template #cell-actions="{ row }">
                        <Link :href="route('club.facturas.show', row.id)" class="text-primary-600 hover:text-primary-900 font-medium text-sm transition-colors duration-200">
                            Ver Detalles
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
