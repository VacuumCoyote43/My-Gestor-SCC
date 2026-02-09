<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
    factura: Object,
});

const form = useForm({
    factura_id: props.factura.id,
    importe: '',
    fecha_pago: new Date().toISOString().split('T')[0],
    metodo_pago: 'transferencia',
    archivos: [],
});

const fileInput = ref(null);

const handleFileChange = (event) => {
    const files = Array.from(event.target.files);
    form.archivos = files;
};

const submit = () => {
    form.post(route('club.pagos.store'), {
        forceFormData: true,
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
};
</script>

<template>
    <Head title="Registrar Pago" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    Registrar Pago a Factura
                </h2>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <!-- Información de la factura -->
                <Card class="mb-6">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Información de la Factura</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <div class="text-sm font-medium text-gray-500">Número</div>
                                <div class="mt-1 text-lg text-gray-900">{{ factura.numero }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Emisor</div>
                                <div class="mt-1 text-lg text-gray-900">{{ factura.emisor?.nombre || factura.emisor?.nombre_legal }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Total de la Factura</div>
                                <div class="mt-1 text-lg font-bold text-gray-900">{{ formatCurrency(factura.total) }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Total Pagado</div>
                                <div class="mt-1 text-lg text-green-600">{{ formatCurrency(factura.total_pagado_registrado ?? factura.total_pagado ?? 0) }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-500">Pendiente</div>
                                <div class="mt-1 text-lg font-bold text-red-600">{{ formatCurrency(factura.total - (factura.total_pagado_registrado ?? factura.total_pagado ?? 0)) }}</div>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Formulario de pago -->
                <Card>
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
                                    :max="factura.total - (factura.total_pagado_registrado ?? factura.total_pagado ?? 0)"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Máximo: {{ formatCurrency(factura.total - (factura.total_pagado_registrado ?? factura.total_pagado ?? 0)) }}
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
                                    class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-200"
                                    required
                                >
                                    <option value="transferencia">Transferencia Bancaria</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="cheque">Cheque</option>
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
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
                                    required
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Puedes subir varios archivos (imágenes o PDF). Máximo 6MB por archivo.
                                </p>
                                <InputError :message="form.errors.archivos" class="mt-2" />
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
                                            <strong>Importante:</strong> El pago debe ser validado por el proveedor para que se considere efectivo. 
                                            Asegúrate de adjuntar un justificante válido (comprobante de transferencia, recibo, etc.).
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-4">
                                <Link :href="route('club.facturas-recibidas.show', factura.id)" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Registrar Pago
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
