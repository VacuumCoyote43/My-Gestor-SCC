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
    club: Object,
    proveedores: Array,
    clubes: Array,
});

const form = useForm({
    receptor_type: 'proveedor',
    receptor_id: '',
    serie: 'CLUB',
    fecha_factura: new Date().toISOString().split('T')[0],
    fecha_vencimiento: '',
    impuestos: 0,
    conceptos: [
        { descripcion: '', cantidad: 1, precio_unitario: 0, tipo_impuesto: 0, total_linea: 0 }
    ],
    archivos: [],
});

const fileInput = ref(null);

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

const handleFileChange = (event) => {
    form.archivos = Array.from(event.target.files);
};

const submit = () => {
    form.post(route('club.facturas.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Nueva Factura" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    Nueva Factura{{ club ? ' - ' + club.nombre : '' }}
                </h2>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Card>
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Receptor -->
                            <div class="mb-4">
                                <InputLabel for="receptor_type" value="Tipo de Receptor *" />
                                <select
                                    id="receptor_type"
                                    v-model="form.receptor_type"
                                    class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-200"
                                    required
                                >
                                    <option value="proveedor">Proveedor</option>
                                    <option value="club">Club</option>
                                </select>
                                <InputError :message="form.errors.receptor_type" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="receptor_id" value="Receptor *" />
                                <select
                                    id="receptor_id"
                                    v-model="form.receptor_id"
                                    class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-200"
                                    required
                                >
                                    <option value="">Selecciona un receptor</option>
                                    <template v-if="form.receptor_type === 'proveedor'">
                                        <option v-for="proveedor in proveedores" :key="proveedor.id" :value="proveedor.id">
                                            {{ proveedor.nombre_legal }}
                                        </option>
                                    </template>
                                    <template v-else>
                                        <option v-for="club in clubes" :key="club.id" :value="club.id">
                                            {{ club.nombre }}
                                        </option>
                                    </template>
                                </select>
                                <InputError :message="form.errors.receptor_id" class="mt-2" />
                            </div>

                            <!-- Datos de la factura -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4">
                                <div>
                                    <InputLabel for="serie" value="Serie *" />
                                    <TextInput
                                        id="serie"
                                        v-model="form.serie"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.serie" class="mt-2" />
                                </div>

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

                            <!-- Archivos adjuntos -->
                            <div class="mb-4">
                                <InputLabel for="archivos" value="Documentos Adjuntos" />
                                <input
                                    ref="fileInput"
                                    type="file"
                                    id="archivos"
                                    @change="handleFileChange"
                                    accept="image/*,.pdf"
                                    multiple
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Puedes subir varios archivos (imágenes o PDF). Máximo 6MB por archivo.
                                </p>
                                <InputError :message="form.errors.archivos" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-4">
                                <Link :href="route('club.facturas.index')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                    Cancelar
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Crear Factura (Borrador)
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
