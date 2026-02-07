<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    asunto: '',
    categoria: 'tecnica',
    prioridad: 'media',
});

const submit = () => {
    form.post(route('incidencias.store'));
};
</script>

<template>
    <Head title="Nueva Incidencia" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight">
                Nueva Incidencia
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="asunto" value="Asunto" />
                                <TextInput
                                    id="asunto"
                                    v-model="form.asunto"
                                    type="text"
                                    class="block w-full mt-1"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.asunto" />
                            </div>

                            <div>
                                <InputLabel for="categoria" value="Categoría" />
                                <select
                                    id="categoria"
                                    v-model="form.categoria"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="tecnica">Técnica</option>
                                    <option value="administrativa">Administrativa</option>
                                    <option value="financiera">Financiera</option>
                                    <option value="otra">Otra</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.categoria" />
                            </div>

                            <div>
                                <InputLabel for="prioridad" value="Prioridad" />
                                <select
                                    id="prioridad"
                                    v-model="form.prioridad"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="baja">Baja</option>
                                    <option value="media">Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.prioridad" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    Crear Incidencia
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
