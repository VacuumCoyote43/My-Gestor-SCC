<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard Administrador
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Accesos rápidos -->
                <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
                    <Link :href="route('admin.users.index')" class="p-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-center">
                        <div class="text-2xl mb-2">👥</div>
                        <div class="font-semibold">Gestionar Usuarios</div>
                    </Link>
                    <Link :href="route('admin.clubes.index')" class="p-4 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-center">
                        <div class="text-2xl mb-2">⚽</div>
                        <div class="font-semibold">Gestionar Clubes</div>
                    </Link>
                    <Link :href="route('admin.proveedores.index')" class="p-4 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition text-center">
                        <div class="text-2xl mb-2">🏢</div>
                        <div class="font-semibold">Gestionar Proveedores</div>
                    </Link>
                    <Link :href="route('incidencias.index')" class="p-4 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-center">
                        <div class="text-2xl mb-2">🚨</div>
                        <div class="font-semibold">Ver Incidencias</div>
                    </Link>
                </div>

                <!-- Estadísticas generales -->
                <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Usuarios</div>
                            <div class="mt-2 text-3xl font-bold text-gray-900">{{ stats.total_usuarios }}</div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Proveedores</div>
                            <div class="mt-2 text-3xl font-bold text-gray-900">{{ stats.total_proveedores }}</div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Total Clubes</div>
                            <div class="mt-2 text-3xl font-bold text-gray-900">{{ stats.total_clubes }}</div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500">Incidencias Abiertas</div>
                            <div class="mt-2 text-3xl font-bold text-red-600">{{ stats.incidencias_abiertas }}</div>
                        </div>
                    </div>
                </div>

                <!-- Facturas por estado -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Facturas por Estado</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                            <div>
                                <div class="text-sm text-gray-500">Borrador</div>
                                <div class="text-2xl font-bold text-gray-700">{{ stats.facturas_por_estado.draft }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Pendiente Pago</div>
                                <div class="text-2xl font-bold text-yellow-600">{{ stats.facturas_por_estado.pending_payment }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Pago Registrado</div>
                                <div class="text-2xl font-bold text-blue-600">{{ stats.facturas_por_estado.payment_registered }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Pagadas</div>
                                <div class="text-2xl font-bold text-green-600">{{ stats.facturas_por_estado.paid }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Canceladas</div>
                                <div class="text-2xl font-bold text-red-600">{{ stats.facturas_por_estado.cancelled }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
