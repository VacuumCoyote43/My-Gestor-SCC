<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    proveedor: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    nombre_legal: props.proveedor?.nombre_legal || '',
    nif_cif: props.proveedor?.nif_cif || '',
    email: props.proveedor?.email || '',
    direccion: props.proveedor?.direccion || '',
    es_liga: props.proveedor?.es_liga || false,
});

const submit = () => {
    form.put(route('admin.proveedores.update', props.proveedor.id));
};
</script>

<template>
    <Head title="Editar Proveedor" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                Editar Proveedor: {{ proveedor.nombre_legal }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="nombre_legal" value="Nombre Legal *" />
                                <TextInput
                                    id="nombre_legal"
                                    v-model="form.nombre_legal"
                                    type="text"
                                    class="block w-full mt-1"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.nombre_legal" />
                            </div>

                            <div>
                                <InputLabel for="nif_cif" value="NIF/CIF *" />
                                <TextInput
                                    id="nif_cif"
                                    v-model="form.nif_cif"
                                    type="text"
                                    class="block w-full mt-1"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.nif_cif" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email *" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="block w-full mt-1"
                                    required
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

                            <div class="flex items-center gap-2">
                                <Checkbox id="es_liga" v-model:checked="form.es_liga" />
                                <InputLabel for="es_liga" value="Es la Liga" class="cursor-pointer" />
                                <InputError class="mt-2" :message="form.errors.es_liga" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    Actualizar Proveedor
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
