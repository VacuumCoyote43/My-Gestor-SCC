<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    user: Object,
});
</script>

<template>
    <Head title="Detalle de Usuario" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detalle de Usuario
                </h2>
                <Link :href="route('admin.users.index')" class="text-blue-600 hover:text-blue-900">
                    Volver al listado
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <div class="text-sm font-medium text-gray-500">Nombre</div>
                                <div class="mt-1 text-lg text-gray-900">{{ user.name }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-gray-500">Email</div>
                                <div class="mt-1 text-lg text-gray-900">{{ user.email }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-gray-500">Rol</div>
                                <div class="mt-1">
                                    <span v-if="user.role" class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ user.role.nombre }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-gray-500">Estado</div>
                                <div class="mt-1">
                                    <span :class="user.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full">
                                        {{ user.active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-gray-500">Último acceso</div>
                                <div class="mt-1 text-lg text-gray-900">
                                    {{ user.last_login_at ? new Date(user.last_login_at).toLocaleString('es-ES') : 'Nunca' }}
                                </div>
                            </div>

                            <div v-if="user.last_login_ip">
                                <div class="text-sm font-medium text-gray-500">IP del último acceso</div>
                                <div class="mt-1 text-lg text-gray-900">{{ user.last_login_ip }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-gray-500">Fecha de creación</div>
                                <div class="mt-1 text-lg text-gray-900">
                                    {{ new Date(user.created_at).toLocaleString('es-ES') }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <Link :href="route('admin.users.edit', user.id)" class="inline-flex items-center px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                Editar Usuario
                            </Link>
                            <Link :href="route('admin.users.index')" class="text-gray-600 hover:text-gray-900">
                                Volver al listado
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
