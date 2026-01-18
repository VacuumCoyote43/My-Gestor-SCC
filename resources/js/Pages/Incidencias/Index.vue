<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    incidencias: Object,
    filters: Object,
});

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'abierta': 'bg-red-100 text-red-800',
        'en_progreso': 'bg-yellow-100 text-yellow-800',
        'cerrada': 'bg-green-100 text-green-800',
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};

const getPrioridadBadgeClass = (prioridad) => {
    const classes = {
        'baja': 'bg-gray-100 text-gray-800',
        'media': 'bg-blue-100 text-blue-800',
        'alta': 'bg-orange-100 text-orange-800',
        'urgente': 'bg-red-100 text-red-800',
    };
    return classes[prioridad] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Incidencias" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Incidencias
                </h2>
                <Link :href="route('incidencias.create')" class="inline-flex items-center px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Nueva Incidencia
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
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Asunto</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Categoría</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Prioridad</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Estado</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Fecha</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="incidencia in incidencias.data" :key="incidencia.id">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ incidencia.asunto }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ incidencia.categoria }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getPrioridadBadgeClass(incidencia.prioridad)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ incidencia.prioridad }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getEstadoBadgeClass(incidencia.estado)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ incidencia.estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ new Date(incidencia.fecha_apertura).toLocaleDateString('es-ES') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                                            <Link :href="route('incidencias.show', incidencia.id)" class="text-blue-600 hover:text-blue-900">
                                                Ver
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
