<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

const props = defineProps({
    cargo: Object,
});

const form = useForm({
    cargo_jugador_id: props.cargo.id,
    importe: '',
    fecha_pago: new Date().toISOString().split('T')[0],
    metodo_pago: 'transferencia',
    justificacion: null,
});

const fileInput = ref(null);

const handleFileChange = (event) => {
    const files = Array.from(event.target.files);
    form.justificacion = files;
};

const submit = () => {
    form.post(route('jugador.pagos.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Registrar Pago" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">
                    Registrar Pago
                </h2>
                <Link :href="route('jugador.cargos.show', cargo.id)" class="text-blue-600 hover:text-blue-900">
                    Volver al cargo
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <!-- Información del cargo -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Información del Cargo</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <div class="text-sm font-medium text-gray-500">Club</div>
                                <div class="mt-1 text-lg text-gray-900">{{ cargo.club?.nombre }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Total del Cargo</div>
                                <div class="mt-1 text-lg font-bold text-gray-900">{{ cargo.total }} €</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Total Pagado</div>
                                <div class="mt-1 text-lg text-green-600">{{ cargo.total_pagado }} €</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Pendiente</div>
                                <div class="mt-1 text-lg font-bold text-red-600">{{ (cargo.total - cargo.total_pagado).toFixed(2) }} €</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de pago -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Datos del Pago</h3>
                        
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <InputLabel for="importe" value="Importe a Pagar *" />
                                <TextInput
                                    id="importe"
                                    v-model="form.importe"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    :max="cargo.total - cargo.total_pagado"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Máximo: {{ (cargo.total - cargo.total_pagado).toFixed(2) }} €
                                </p>
                                <InputError :message="form.errors.importe" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="fecha_pago" value="Fecha del Pago *" />
                                <TextInput
                                    id="fecha_pago"
                                    v-model="form.fecha_pago"
                                    type="date"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.fecha_pago" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="metodo_pago" value="Método de Pago *" />
                                <select
                                    id="metodo_pago"
                                    v-model="form.metodo_pago"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="transferencia">Transferencia Bancaria</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="bizum">Bizum</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <InputError :message="form.errors.metodo_pago" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="justificacion" value="Justificante del Pago *" />
                                <input
                                    ref="fileInput"
                                    type="file"
                                    id="justificacion"
                                    @change="handleFileChange"
                                    accept="image/*,.pdf"
                                    multiple
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    required
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Puedes subir varios archivos (imágenes o PDF). Máximo 6MB por archivo.
                                </p>
                                <InputError :message="form.errors.justificacion" class="mt-2" />
                            </div>

                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            <strong>Importante:</strong> El pago debe ser validado por el gestor del club para que se considere efectivo. 
                                            Asegúrate de adjuntar un justificante válido (comprobante de transferencia, recibo, etc.).
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <Link :href="route('jugador.cargos.show', cargo.id)" class="text-gray-600 hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Registrar Pago
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
