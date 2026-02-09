<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import Card from '@/Components/Card.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">
                        Dashboard Administrador
                    </h2>
                    <p class="mt-1 text-gray-600">
                        Visión general del sistema
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Accesos rápidos -->
                <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-4">
                    <Link :href="route('admin.users.index')" class="group relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105">
                        <div class="relative z-10">
                            <div class="text-4xl mb-3">👥</div>
                            <div class="text-lg font-semibold">Gestionar Usuarios</div>
                            <div class="text-sm text-blue-100 mt-1">Administrar accesos</div>
                        </div>
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10"></div>
                    </Link>
                    
                    <Link :href="route('admin.clubes.index')" class="group relative overflow-hidden bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105">
                        <div class="relative z-10">
                            <div class="text-4xl mb-3">⚽</div>
                            <div class="text-lg font-semibold">Gestionar Clubes</div>
                            <div class="text-sm text-green-100 mt-1">Configurar clubes</div>
                        </div>
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10"></div>
                    </Link>
                    
                    <Link :href="route('admin.proveedores.index')" class="group relative overflow-hidden bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105">
                        <div class="relative z-10">
                            <div class="text-4xl mb-3">🏢</div>
                            <div class="text-lg font-semibold">Gestionar Proveedores</div>
                            <div class="text-sm text-purple-100 mt-1">Administrar proveedores</div>
                        </div>
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10"></div>
                    </Link>
                    
                    <Link :href="route('incidencias.index')" class="group relative overflow-hidden bg-gradient-to-br from-red-500 to-red-600 text-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105">
                        <div class="relative z-10">
                            <div class="text-4xl mb-3">🚨</div>
                            <div class="text-lg font-semibold">Ver Incidencias</div>
                            <div class="text-sm text-red-100 mt-1">Gestionar tickets</div>
                        </div>
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-white opacity-10"></div>
                    </Link>
                </div>

                <!-- Estadísticas generales -->
                <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        title="Total Usuarios"
                        :value="stats.total_usuarios"
                        icon="👥"
                        color="blue"
                    />
                    
                    <StatCard
                        title="Total Proveedores"
                        :value="stats.total_proveedores"
                        icon="🏢"
                        color="purple"
                    />
                    
                    <StatCard
                        title="Total Clubes"
                        :value="stats.total_clubes"
                        icon="⚽"
                        color="green"
                    />
                    
                    <StatCard
                        title="Incidencias Abiertas"
                        :value="stats.incidencias_abiertas"
                        icon="🚨"
                        color="red"
                    />
                </div>

                <!-- Facturas por estado -->
                <Card>
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Facturas por Estado</h3>
                        <p class="text-sm text-gray-500 mt-1">Distribución de facturas en el sistema</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Borrador</div>
                            <div class="text-3xl font-bold text-gray-700">{{ stats.facturas_por_estado.draft }}</div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <div class="text-xs font-medium text-yellow-700 uppercase tracking-wide mb-2">Pendiente Pago</div>
                            <div class="text-3xl font-bold text-yellow-600">{{ stats.facturas_por_estado.pending_payment }}</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <div class="text-xs font-medium text-blue-700 uppercase tracking-wide mb-2">Pago Registrado</div>
                            <div class="text-3xl font-bold text-blue-600">{{ stats.facturas_por_estado.payment_registered }}</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="text-xs font-medium text-green-700 uppercase tracking-wide mb-2">Pagadas</div>
                            <div class="text-3xl font-bold text-green-600">{{ stats.facturas_por_estado.paid }}</div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                            <div class="text-xs font-medium text-red-700 uppercase tracking-wide mb-2">Canceladas</div>
                            <div class="text-3xl font-bold text-red-600">{{ stats.facturas_por_estado.cancelled }}</div>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
