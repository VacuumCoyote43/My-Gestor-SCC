<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';

defineProps({
    proveedor: Object,
});
</script>

<template>
    <Head title="Detalles del Proveedor" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-900">
                Detalles del Proveedor: {{ proveedor.nombre_legal }}
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <Card>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Nombre Legal</h3>
                                <p class="mt-1 text-lg text-gray-900">{{ proveedor.nombre_legal }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">NIF/CIF</h3>
                                <p class="mt-1 text-lg text-gray-900">{{ proveedor.nif_cif }}</p>
                            </div>

                             <div>
                                <h3 class="text-sm font-medium text-gray-500">Email</h3>
                                <p class="mt-1 text-lg text-gray-900">{{ proveedor.email }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Tipo</h3>
                                <p class="mt-1">
                                    <span v-if="proveedor.es_liga" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Liga
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Proveedor
                                    </span>
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500">Dirección</h3>
                                <p class="mt-1 text-lg text-gray-900 whitespace-pre-line">{{ proveedor.direccion || 'No especificada' }}</p>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-4 border-t border-gray-100 pt-4">
                            <Link
                                :href="route('admin.proveedores.edit', proveedor.id)"
                                class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-primary-500/50 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-500/50 focus:outline-none focus:ring-4 focus:ring-primary-300 active:scale-95"
                            >
                                Editar
                            </Link>
                            <Link
                                :href="route('admin.proveedores.index')"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            >
                                Volver
                            </Link>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
