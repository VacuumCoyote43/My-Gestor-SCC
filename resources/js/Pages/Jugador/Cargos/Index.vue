<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    cargos: Object,
    filters: Object,
});

const estadoFilter = ref(props.filters.estado || '');
const mesFilter = ref(props.filters.mes || '');

const columns = [
    { key: 'club', label: 'Club', sortable: false },
    { key: 'fecha_emision', label: 'Fecha Emisión', sortable: true },
    { key: 'total', label: 'Total', sortable: true, class: 'text-right', rowClass: 'text-right font-medium' },
    { key: 'estado', label: 'Estado', sortable: true },
    { key: 'actions', label: 'Acciones', sortable: false, class: 'text-right', rowClass: 'text-right' },
];

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
};

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'draft': 'bg-gray-100 text-gray-800',
        'pending_payment': 'bg-yellow-100 text-yellow-800',
        'payment_registered': 'bg-blue-100 text-blue-800',
        'paid': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};

const getEstadoLabel = (estado) => {
    const labels = {
        'draft': 'Borrador',
        'pending_payment': 'Pendiente Pago',
        'payment_registered': 'Pago Registrado',
        'paid': 'Pagado',
        'cancelled': 'Cancelado',
    };
    return labels[estado] || estado;
};

const applyFilters = () => {
    router.get(route('jugador.cargos.index'), {
        estado: estadoFilter.value,
        mes: mesFilter.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Mis Cargos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-900">
                Mis Cargos
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Filtros -->
                <SearchFilter
                    :filters="filters"
                    route-name="jugador.cargos.index"
                    placeholder="Buscar por club..."
                    :show-filters="true"
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
                                    <option value="pending_payment">Pendiente de Pago</option>
                                    <option value="payment_registered">Pago Registrado</option>
                                    <option value="paid">Pagado</option>
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

                <DataTable :rows="cargos" :columns="columns" :filters="filters" route-name="jugador.cargos.index" :sticky-header="true">
                    <template #cell-club="{ row }">
                        <span class="text-gray-900">{{ row.club?.nombre }}</span>
                    </template>
                    <template #cell-fecha_emision="{ value }">
                        {{ new Date(value).toLocaleDateString('es-ES') }}
                    </template>
                    <template #cell-total="{ value }">
                        <span class="font-semibold text-gray-900">{{ formatCurrency(value) }}</span>
                    </template>
                    <template #cell-estado="{ row }">
                        <span :class="getEstadoBadgeClass(row.estado)" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                            {{ getEstadoLabel(row.estado) }}
                        </span>
                    </template>
                    <template #cell-actions="{ row }">
                        <Link :href="route('jugador.cargos.show', row.id)" class="text-sm font-medium text-blue-600 transition hover:text-blue-900">
                            Ver / Pagar
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
