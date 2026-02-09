<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
    user: Object,
    roles: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role_id: props.user.role_id,
    active: props.user.active,
});

const submit = () => {
    form.put(route('admin.users.update', props.user.id));
};
</script>

<template>
    <Head title="Editar Usuario" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    Editar Usuario
                </h2>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <Card>
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <InputLabel for="name" value="Nombre" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="role_id" value="Rol" />
                                <select
                                    id="role_id"
                                    v-model="form.role_id"
                                    class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-200"
                                    required
                                >
                                    <option value="">Selecciona un rol</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.nombre }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.role_id" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.active"
                                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-200"
                                    />
                                    <span class="ml-2 text-sm text-gray-600">Usuario activo</span>
                                </label>
                                <InputError :message="form.errors.active" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-4">
                                <Link :href="route('admin.users.index')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Actualizar Usuario
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
