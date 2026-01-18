<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    facturas: Object,
    filters: Object,
    club: Object,
});

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
</script>

<template>
    <Head title="Facturas Emitidas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Facturas Emitidas{{ club ? ' - ' + club.nombre : '' }}
                </h2>
                <Link :href="route('club.facturas.create')" class="inline-flex items-center px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Nueva Factura
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
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
                                            <Link :href="route('club.facturas.show', factura.id)" class="text-blue-600 hover:text-blue-900">
                                                Ver
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="facturas.data.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500">
                                            No hay facturas emitidas
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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
