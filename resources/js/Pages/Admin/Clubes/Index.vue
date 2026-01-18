<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    clubes: Object,
    filters: Object,
});
</script>

<template>
    <Head title="Gestión de Clubes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Gestión de Clubes
                </h2>
                <Link :href="route('admin.clubes.create')" class="inline-flex items-center px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Nuevo Club
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
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nombre</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">CIF</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Creado</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="club in clubes.data" :key="club.id">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            {{ club.nombre }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ club.cif || '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ club.email || '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ new Date(club.created_at).toLocaleDateString('es-ES') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                                            <Link :href="route('admin.clubes.edit', club.id)" class="text-blue-600 hover:text-blue-900 mr-3">
                                                Editar
                                            </Link>
                                            <Link :href="route('admin.clubes.show', club.id)" class="text-green-600 hover:text-green-900">
                                                Ver
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div v-if="clubes.links" class="flex justify-between items-center mt-4">
                            <div class="text-sm text-gray-700">
                                Mostrando {{ clubes.from }} a {{ clubes.to }} de {{ clubes.total }} resultados
                            </div>
                            <div class="flex gap-1">
                                <component v-for="link in clubes.links" :key="link.label" :is="link.url ? Link : 'span'" :href="link.url" :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-3 py-2 text-sm border rounded" v-html="link.label"></component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
