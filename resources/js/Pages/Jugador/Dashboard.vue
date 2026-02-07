<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    deuda: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
};
</script>

<template>
    <Head title="Dashboard Jugador" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight">
                Dashboard Jugador
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Resumen de deuda -->
                <div class="mb-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Deuda Total</div>
                                <div class="mt-2 text-3xl font-bold" :class="deuda.total_deuda > 0 ? 'text-red-600' : 'text-green-600'">
                                    {{ formatCurrency(deuda.total_deuda) }}
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Cargos Pendientes</div>
                                <div class="mt-2 text-3xl font-bold text-yellow-600">
                                    {{ deuda.cantidad_cargos_pendientes }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalle de cargos pendientes -->
                <div v-if="deuda.detalles.length > 0" class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Detalle de Cargos Pendientes</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Club</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Fecha Emisión</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Total</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Pagado</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Pendiente</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="cargo in deuda.detalles" :key="cargo.cargo_id">
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ cargo.club }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ cargo.fecha_emision }}</td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-900 whitespace-nowrap">{{ formatCurrency(cargo.total) }}</td>
                                        <td class="px-6 py-4 text-sm text-right text-green-600 whitespace-nowrap">{{ formatCurrency(cargo.pagado) }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-right text-red-600 whitespace-nowrap">{{ formatCurrency(cargo.pendiente) }}</td>
                                        <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                                            <Link :href="route('jugador.cargos.show', cargo.cargo_id)" class="text-blue-600 hover:text-blue-900">
                                                Ver / Pagar
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div v-else class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        No tienes cargos pendientes. ¡Estás al día!
                    </div>
                </div>

                <div class="mt-6">
                    <Link :href="route('jugador.cargos.index')" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-primary-500/50 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-500/50 focus:outline-none focus:ring-4 focus:ring-primary-300 active:scale-95">
                        Ver Todos los Cargos
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
