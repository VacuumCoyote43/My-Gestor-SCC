<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import Card from '@/Components/Card.vue';
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
                <Link :href="route('admin.clubes.create')" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-primary-500/50 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-500/50 focus:outline-none focus:ring-4 focus:ring-primary-300 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Club
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Buscador -->
                <SearchFilter
                    :filters="filters"
                    route-name="admin.clubes.index"
                    placeholder="Buscar clubes por nombre o CIF..."
                />

                <Card :padding="false">
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
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
