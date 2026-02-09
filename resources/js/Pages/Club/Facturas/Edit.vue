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
    club: Object,
    proveedores: Array,
    clubes: Array,
});

const form = useForm({
    fecha_factura: props.factura.fecha_factura,
    fecha_vencimiento: props.factura.fecha_vencimiento || '',
    impuestos: props.factura.impuestos || 0,
    conceptos: props.factura.conceptos.map(c => ({
        descripcion: c.descripcion,
        cantidad: c.cantidad,
        precio_unitario: c.precio_unitario,
        tipo_impuesto: c.tipo_impuesto,
        total_linea: c.total_linea
    })),
});

const agregarConcepto = () => {
    form.conceptos.push({ descripcion: '', cantidad: 1, precio_unitario: 0, tipo_impuesto: 0, total_linea: 0 });
};

const eliminarConcepto = (index) => {
    if (form.conceptos.length > 1) {
        form.conceptos.splice(index, 1);
    }
};

const calcularTotalLinea = (concepto) => {
    const subtotal = concepto.cantidad * concepto.precio_unitario;
    const impuesto = subtotal * (concepto.tipo_impuesto / 100);
    concepto.total_linea = parseFloat((subtotal + impuesto).toFixed(2));
};

const calcularTotal = () => {
    return form.conceptos.reduce((sum, concepto) => sum + concepto.total_linea, 0);
};

const submit = () => {
    form.put(route('club.facturas.update', props.factura.id));
};
</script>

<template>
    <Head title="Editar Factura" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    Editar Factura{{ club ? ' - ' + club.nombre : '' }}
                </h2>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Card>
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Receptor (no editable) -->
                            <div class="mb-4 rounded-xl border border-gray-200 bg-gray-50/60 p-4">
                                <p class="text-sm text-gray-600">
                                    <strong>Receptor:</strong> {{ factura.receptor?.nombre || factura.receptor?.nombre_legal }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    El receptor no puede modificarse una vez creada la factura
                                </p>
                            </div>

                            <!-- Datos de la factura -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4">
                                <div>
                                    <InputLabel for="fecha_factura" value="Fecha Factura *" />
                                    <TextInput
                                        id="fecha_factura"
                                        v-model="form.fecha_factura"
                                        type="date"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.fecha_factura" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="fecha_vencimiento" value="Fecha Vencimiento" />
                                    <TextInput
                                        id="fecha_vencimiento"
                                        v-model="form.fecha_vencimiento"
                                        type="date"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.fecha_vencimiento" class="mt-2" />
                                </div>
                            </div>

                            <!-- Conceptos -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <InputLabel value="Conceptos *" />
                                    <button type="button" @click="agregarConcepto" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                                        + Agregar concepto
                                    </button>
                                </div>

                                <div v-for="(concepto, index) in form.conceptos" :key="index" class="mb-4 rounded-xl border border-gray-200 bg-gray-50/60 p-4">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                                        <div class="md:col-span-2">
                                            <InputLabel :for="'descripcion_' + index" value="Descripción" />
                                            <TextInput
                                                :id="'descripcion_' + index"
                                                v-model="concepto.descripcion"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <InputLabel :for="'cantidad_' + index" value="Cantidad" />
                                            <TextInput
                                                :id="'cantidad_' + index"
                                                v-model.number="concepto.cantidad"
                                                type="number"
                                                step="1"
                                                min="1"
                                                class="mt-1 block w-full"
                                                @input="calcularTotalLinea(concepto)"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <InputLabel :for="'precio_' + index" value="Precio" />
                                            <TextInput
                                                :id="'precio_' + index"
                                                v-model.number="concepto.precio_unitario"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                class="mt-1 block w-full"
                                                @input="calcularTotalLinea(concepto)"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <InputLabel :for="'impuesto_' + index" value="% IVA" />
                                            <TextInput
                                                :id="'impuesto_' + index"
                                                v-model.number="concepto.tipo_impuesto"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                class="mt-1 block w-full"
                                                @input="calcularTotalLinea(concepto)"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-sm font-medium text-gray-700">
                                            Total línea: {{ concepto.total_linea.toFixed(2) }} €
                                        </span>
                                        <button v-if="form.conceptos.length > 1" type="button" @click="eliminarConcepto(index)" class="text-sm font-medium text-red-600 hover:text-red-800">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="mb-4 rounded-xl border border-gray-200 bg-white p-4">
                                <div class="text-right">
                                    <span class="text-lg font-bold text-gray-900">
                                        Total Factura: {{ calcularTotal().toFixed(2) }} €
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-4">
                                <Link :href="route('club.facturas.show', factura.id)" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Actualizar Factura
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
