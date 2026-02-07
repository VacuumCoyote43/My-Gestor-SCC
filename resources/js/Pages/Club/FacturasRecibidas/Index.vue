<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import Card from '@/Components/Card.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    facturas: Object,
    filters: Object,
    club: Object,
});

const estadoFilter = ref(props.filters.estado || '');
const mesFilter = ref(props.filters.mes || '');

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
        'paid': 'Pagada',
        'cancelled': 'Cancelada',
    };
    return labels[estado] || estado;
};

const applyFilters = () => {
    router.get(route('club.facturas-recibidas.index'), {
        search: props.filters.search,
        estado: estadoFilter.value,
        mes: mesFilter.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Facturas Recibidas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                Facturas Recibidas{{ club ? ' - ' + club.nombre : '' }}
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Buscador -->
                <SearchFilter
                    :filters="filters"
                    route-name="club.facturas-recibidas.index"
                    placeholder="Buscar facturas por número o emisor..."
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

                <Card :padding="false">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Número</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Receptor</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Fecha</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Total</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Estado</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="factura in facturas.data" :key="factura.id">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            {{ factura.numero || 'BORRADOR' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ factura.receptor?.nombre || factura.receptor?.nombre_legal }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ new Date(factura.fecha_factura).toLocaleDateString('es-ES') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-right text-gray-900 whitespace-nowrap">
                                            {{ formatCurrency(factura.total) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getEstadoBadgeClass(factura.estado)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ factura.estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                                            <Link :href="route('club.facturas-recibidas.show', factura.id)" class="text-blue-600 hover:text-blue-900">
                                                Ver
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="facturas.data.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500">
                                            No hay facturas recibidas
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div v-if="facturas.links" class="flex justify-between items-center mt-4">
                            <div class="text-sm text-gray-700">
                                Mostrando {{ facturas.from }} a {{ facturas.to }} de {{ facturas.total }} resultados
                            </div>
                            <div class="flex gap-1">
                                <component v-for="link in facturas.links" :key="link.label" :is="link.url ? Link : 'span'" :href="link.url" :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-3 py-2 text-sm border rounded" v-html="link.label"></component>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
