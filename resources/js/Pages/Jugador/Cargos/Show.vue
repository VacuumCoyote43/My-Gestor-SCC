<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    cargo: Object,
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
    return ['pending_payment', 'payment_registered'].includes(props.cargo.estado);
};
</script>

<template>
    <Head title="Detalle Cargo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">
                    Mi Cargo #{{ cargo.id }}
                </h2>
                <Link v-if="puedeRegistrarPago()" :href="route('jugador.cargos.pagos.create', cargo.id)" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-green-600 to-green-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-green-500/50 transition-all duration-200 hover:from-green-700 hover:to-green-800 hover:shadow-xl hover:shadow-green-500/50 focus:outline-none focus:ring-4 focus:ring-green-300 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Registrar Pago
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Información básica -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Cargo</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Club</dt>
                                        <dd class="text-sm text-gray-900">{{ cargo.club.nombre }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Fecha Emisión</dt>
                                        <dd class="text-sm text-gray-900">{{ new Date(cargo.fecha_emision).toLocaleDateString('es-ES') }}</dd>
                                    </div>
                                    <div v-if="cargo.fecha_vencimiento">
                                        <dt class="text-sm font-medium text-gray-500">Fecha Vencimiento</dt>
                                        <dd class="text-sm text-gray-900">{{ new Date(cargo.fecha_vencimiento).toLocaleDateString('es-ES') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                        <dd>
                                            <span :class="getEstadoBadgeClass(cargo.estado)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ cargo.estado }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conceptos -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Conceptos</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Descripción</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Importe</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="concepto in cargo.conceptos" :key="concepto.id">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ concepto.descripcion }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-right text-gray-900">{{ formatCurrency(concepto.total_linea) }}</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-semibold text-right text-gray-900">Total Cargo</td>
                                        <td class="px-6 py-4 text-lg font-bold text-right text-gray-900">{{ formatCurrency(cargo.total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagos realizados -->
                <div v-if="cargo.pagos && cargo.pagos.length > 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mis Pagos</h3>
                        <div class="space-y-4">
                            <div v-for="pago in cargo.pagos" :key="pago.id" class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ formatCurrency(pago.importe) }}</span>
                                        <span class="ml-2 text-sm text-gray-500">{{ new Date(pago.fecha_pago).toLocaleDateString('es-ES') }}</span>
                                        <span class="ml-2 text-sm text-gray-500">{{ pago.metodo_pago }}</span>
                                    </div>
                                    <span :class="pago.estado_pago === 'validado' ? 'bg-green-100 text-green-800' : pago.estado_pago === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ pago.estado_pago }}
                                    </span>
                                </div>
                                <div v-if="pago.archivo_adjuntos && pago.archivo_adjuntos.length > 0" class="mt-2">
                                    <p class="text-xs text-gray-500 mb-1">Justificantes:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <a v-for="archivo in pago.archivo_adjuntos" :key="archivo.id" :href="route('archivos.download', archivo.id)" class="text-xs text-blue-600 hover:text-blue-900">
                                            📎 {{ archivo.nombre_original }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensaje si no hay pagos -->
                <div v-else class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        No has registrado ningún pago aún.
                        <Link v-if="puedeRegistrarPago()" :href="route('jugador.cargos.pagos.create', cargo.id)" class="block mt-4 text-blue-600 hover:text-blue-900">
                            Registrar tu primer pago
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
