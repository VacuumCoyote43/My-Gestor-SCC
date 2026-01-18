<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    club: Object,
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
    <Head title="Dashboard Club" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard Gestor Club
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="club" class="mb-6">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900">{{ club.nombre }}</h3>
                            <p v-if="club.cif" class="text-sm text-gray-600">CIF: {{ club.cif }}</p>
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
                                <div class="text-sm font-medium text-gray-500">Pagado a Proveedores</div>
                                <div class="mt-2 text-2xl font-bold text-red-600">
                                    {{ formatCurrency(balance.total_pagado_proveedores) }}
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Cargos Emitidos</div>
                                <div class="mt-2 text-2xl font-bold text-blue-600">
                                    {{ formatCurrency(balance.total_cargos_emitidos) }}
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Cobrado de Jugadores</div>
                                <div class="mt-2 text-2xl font-bold text-green-600">
                                    {{ formatCurrency(balance.total_cobrado_jugadores) }}
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="text-sm font-medium text-gray-500">Pendiente Cobro</div>
                                <div class="mt-2 text-2xl font-bold text-yellow-600">
                                    {{ formatCurrency(balance.pendiente_cobro_jugadores) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <Link :href="route('club.cargos.index')" class="inline-flex items-center px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Ver Cargos
                    </Link>
                    <Link :href="route('club.cargos.create')" class="inline-flex items-center px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700">
                        Nuevo Cargo
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
