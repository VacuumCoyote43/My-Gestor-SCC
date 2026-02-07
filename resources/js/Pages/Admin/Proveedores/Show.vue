<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    proveedor: Object,
});
</script>

<template>
    <Head title="Detalles del Proveedor" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                Detalles del Proveedor: {{ proveedor.nombre_legal }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
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

                        <div class="mt-8 flex justify-end gap-4">
                            <Link
                                :href="route('admin.proveedores.edit', proveedor.id)"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Editar
                            </Link>
                            <Link
                                :href="route('admin.proveedores.index')"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Volver
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
