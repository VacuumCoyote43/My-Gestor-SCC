<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';

const props = defineProps({
    factura: Object,
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

const puedeRegistrarPago = () => {
    return ['pending_payment', 'payment_registered'].includes(props.factura.estado);
};
</script>

<template>
    <Head title="Detalle Factura" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    Factura {{ factura.numero }}
                </h2>
                <Link v-if="puedeRegistrarPago()" :href="route('club.facturas.pagos.create', factura.id)" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-green-600 to-green-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-green-500/50 transition-all duration-200 hover:from-green-700 hover:to-green-800 hover:shadow-xl hover:shadow-green-500/50 focus:outline-none focus:ring-4 focus:ring-green-300 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Registrar Pago
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Información básica -->
                <Card>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información de la Factura</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Número</dt>
                                        <dd class="text-sm text-gray-900">{{ factura.numero }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Serie</dt>
                                        <dd class="text-sm text-gray-900">{{ factura.serie }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Fecha Factura</dt>
                                        <dd class="text-sm text-gray-900">{{ new Date(factura.fecha_factura).toLocaleDateString('es-ES') }}</dd>
                                    </div>
                                    <div v-if="factura.fecha_vencimiento">
                                        <dt class="text-sm font-medium text-gray-500">Fecha Vencimiento</dt>
                                        <dd class="text-sm text-gray-900">{{ new Date(factura.fecha_vencimiento).toLocaleDateString('es-ES') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                        <dd>
                                            <span :class="getEstadoBadgeClass(factura.estado)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ factura.estado }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Emisor</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                                        <dd class="text-sm text-gray-900">{{ factura.emisor?.nombre || factura.emisor?.nombre_legal }}</dd>
                                    </div>
                                    <div v-if="factura.emisor?.email">
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="text-sm text-gray-900">{{ factura.emisor.email }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Conceptos -->
                <Card>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Conceptos</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Descripción</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="concepto in factura.conceptos" :key="concepto.id">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ concepto.descripcion }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-right text-gray-900">{{ formatCurrency(concepto.total_linea) }}</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-semibold text-right text-gray-900">Total Factura</td>
                                        <td class="px-6 py-4 text-lg font-bold text-right text-gray-900">{{ formatCurrency(factura.total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </Card>

                <!-- Documentos Adjuntos -->
                <Card v-if="factura.archivo_adjuntos?.length > 0">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Documentos Adjuntos</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div v-for="archivo in factura.archivo_adjuntos" :key="archivo.id" class="rounded-xl border border-gray-200 bg-gray-50/60 p-4 transition hover:border-primary-300">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" :title="archivo.nombre_original">
                                            📄 {{ archivo.nombre_original }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ (archivo.tamano / 1024).toFixed(2) }} KB
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2 mt-3">
                                    <a :href="route('archivos.view', archivo.id)" target="_blank" class="text-xs font-medium text-primary-600 hover:text-primary-800">
                                        Ver
                                    </a>
                                    <a :href="route('archivos.download', archivo.id)" class="text-xs font-medium text-emerald-600 hover:text-emerald-800">
                                        Descargar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Pagos -->
                <Card v-if="factura.pagos && factura.pagos.length > 0">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pagos Registrados</h3>
                        <div class="space-y-4">
                            <div v-for="pago in factura.pagos" :key="pago.id" class="rounded-xl border border-gray-200 bg-gray-50/60 p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ formatCurrency(pago.importe) }}</span>
                                        <span class="ml-2 text-sm text-gray-500">{{ new Date(pago.fecha_pago).toLocaleDateString('es-ES') }}</span>
                                        <span class="ml-2 text-sm text-gray-500">{{ pago.metodo_pago }}</span>
                                    </div>
                                    <span :class="pago.estado_pago === 'validado' ? 'bg-green-100 text-green-800' : pago.estado_pago === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ pago.estado_pago }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
