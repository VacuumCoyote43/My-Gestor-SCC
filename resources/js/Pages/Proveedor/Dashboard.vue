<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    proveedor: Object,
    balance: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
};
</script>

<template>
    <Head title="Dashboard Proveedor" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                Dashboard Proveedor
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="proveedor" class="mb-6">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900">{{ proveedor.nombre_legal }}</h3>
                            <p class="text-sm text-gray-600">NIF/CIF: {{ proveedor.nif_cif }}</p>
                        </div>
                    </div>
                </div>

                <div v-if="balance" class="mb-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">
                        Balance Mensual - {{ balance.periodo.mes_nombre }} {{ balance.periodo.year }}
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Total Emitido</div>
                                <div class="mt-2 text-2xl font-bold text-blue-600">
                                    {{ formatCurrency(balance.total_emitido) }}
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Total Cobrado</div>
                                <div class="mt-2 text-2xl font-bold text-green-600">
                                    {{ formatCurrency(balance.total_cobrado) }}
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Pendiente Cobro</div>
                                <div class="mt-2 text-2xl font-bold text-yellow-600">
                                    {{ formatCurrency(balance.pendiente_cobro) }}
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Balance</div>
                                <div class="mt-2 text-2xl font-bold" :class="balance.balance >= 0 ? 'text-green-600' : 'text-red-600'">
                                    {{ formatCurrency(balance.balance) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <Link :href="route('proveedor.facturas.index')" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-primary-500/50 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-500/50 focus:outline-none focus:ring-4 focus:ring-primary-300 active:scale-95">
                        Ver Facturas
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                    <Link :href="route('proveedor.facturas.create')" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-green-600 to-green-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-green-500/50 transition-all duration-200 hover:from-green-700 hover:to-green-800 hover:shadow-xl hover:shadow-green-500/50 focus:outline-none focus:ring-4 focus:ring-green-300 active:scale-95">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nueva Factura
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
