<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    nombre: '',
    cif: '',
    email: '',
    direccion: '',
});

const submit = () => {
    form.post(route('admin.clubes.store'));
};
</script>

<template>
    <Head title="Nuevo Club" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Nuevo Club
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="nombre" value="Nombre *" />
                                <TextInput
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    class="block w-full mt-1"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.nombre" />
                            </div>

                            <div>
                                <InputLabel for="cif" value="CIF" />
                                <TextInput
                                    id="cif"
                                    v-model="form.cif"
                                    type="text"
                                    class="block w-full mt-1"
                                />
                                <InputError class="mt-2" :message="form.errors.cif" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="block w-full mt-1"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div>
                                <InputLabel for="direccion" value="Dirección" />
                                <textarea
                                    id="direccion"
                                    v-model="form.direccion"
                                    rows="3"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.direccion" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    Crear Club
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
