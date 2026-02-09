<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
    clubes: Array,
    proveedores: Array,
});

const form = useForm({
    serie: 'FAC',
    fecha_factura: new Date().toISOString().split('T')[0],
    fecha_vencimiento: null,
    receptor_type: 'App\\Models\\Club',
    receptor_id: null,
    conceptos: [
        { descripcion: '', cantidad: 1, precio_unitario: 0, tipo_impuesto: 0, total_linea: 0 }
    ],
    archivos: [],
});

const receptores = ref([]);

const updateReceptores = () => {
    receptores.value = form.receptor_type === 'App\\Models\\Club' ? props.clubes : props.proveedores;
    form.receptor_id = null;
};

updateReceptores();

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
    concepto.total_linea = subtotal + impuesto;
};

const handleFileChange = (event) => {
    form.archivos = Array.from(event.target.files);
};

const submit = () => {
    form.post(route('proveedor.facturas.store'));
};
</script>

<template>
    <Head title="Nueva Factura" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-900">
                Nueva Factura
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <Card>
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Datos básicos -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                <div>
                                    <InputLabel for="serie" value="Serie *" />
                                    <TextInput
                                        id="serie"
                                        v-model="form.serie"
                                        type="text"
                                        class="block w-full mt-1"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.serie" />
                                </div>

                                <div>
                                    <InputLabel for="fecha_factura" value="Fecha Factura *" />
                                    <TextInput
                                        id="fecha_factura"
                                        v-model="form.fecha_factura"
                                        type="date"
                                        class="block w-full mt-1"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.fecha_factura" />
                                </div>

                                <div>
                                    <InputLabel for="fecha_vencimiento" value="Fecha Vencimiento" />
                                    <TextInput
                                        id="fecha_vencimiento"
                                        v-model="form.fecha_vencimiento"
                                        type="date"
                                        class="block w-full mt-1"
                                    />
                                    <InputError class="mt-2" :message="form.errors.fecha_vencimiento" />
                                </div>
                            </div>

                            <!-- Receptor -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <InputLabel for="receptor_type" value="Tipo de Receptor *" />
                                    <select
                                        id="receptor_type"
                                        v-model="form.receptor_type"
                                        @change="updateReceptores"
                                    class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-200"
                                        required
                                    >
                                        <option value="App\Models\Club">Club</option>
                                        <option value="App\Models\Proveedor">Proveedor</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.receptor_type" />
                                </div>

                                <div>
                                    <InputLabel for="receptor_id" value="Receptor *" />
                                    <select
                                        id="receptor_id"
                                        v-model="form.receptor_id"
                                    class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-200"
                                        required
                                    >
                                        <option :value="null">Seleccione...</option>
                                        <option v-for="receptor in receptores" :key="receptor.id" :value="receptor.id">
                                            {{ receptor.nombre || receptor.nombre_legal }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.receptor_id" />
                                </div>
                            </div>

                            <!-- Conceptos -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Conceptos de Factura</h3>
                                    <SecondaryButton type="button" @click="agregarConcepto">
                                        + Agregar Concepto
                                    </SecondaryButton>
                                </div>

                                <div v-for="(concepto, index) in form.conceptos" :key="index" class="mb-4 rounded-xl border border-gray-200 bg-gray-50/60 p-4">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                                        <div class="md:col-span-2">
                                            <InputLabel :for="`descripcion_${index}`" value="Descripción *" />
                                            <TextInput
                                                :id="`descripcion_${index}`"
                                                v-model="concepto.descripcion"
                                                type="text"
                                                class="block w-full mt-1"
                                                required
                                            />
                                        </div>

                                        <div>
                                            <InputLabel :for="`cantidad_${index}`" value="Cantidad" />
                                            <TextInput
                                                :id="`cantidad_${index}`"
                                                v-model.number="concepto.cantidad"
                                                type="number"
                                                step="0.01"
                                                class="block w-full mt-1"
                                                @input="calcularTotalLinea(concepto)"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel :for="`precio_${index}`" value="Precio Unit." />
                                            <TextInput
                                                :id="`precio_${index}`"
                                                v-model.number="concepto.precio_unitario"
                                                type="number"
                                                step="0.01"
                                                class="block w-full mt-1"
                                                @input="calcularTotalLinea(concepto)"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel :for="`impuesto_${index}`" value="% Impuesto" />
                                            <TextInput
                                                :id="`impuesto_${index}`"
                                                v-model.number="concepto.tipo_impuesto"
                                                type="number"
                                                step="0.01"
                                                class="block w-full mt-1"
                                                @input="calcularTotalLinea(concepto)"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel :for="`total_${index}`" value="Total *" />
                                            <TextInput
                                                :id="`total_${index}`"
                                                v-model.number="concepto.total_linea"
                                                type="number"
                                                step="0.01"
                                                class="block w-full mt-1"
                                                required
                                            />
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <button
                                            v-if="form.conceptos.length > 1"
                                            type="button"
                                            @click="eliminarConcepto(index)"
                                            class="text-sm font-medium text-red-600 hover:text-red-900"
                                        >
                                            Eliminar concepto
                                        </button>
                                    </div>
                                </div>

                                <InputError class="mt-2" :message="form.errors.conceptos" />
                            </div>

                            <!-- Archivos adjuntos -->
                            <div>
                                <InputLabel for="archivos" value="Archivos Adjuntos (máx. 6MB cada uno)" />
                                <input
                                    id="archivos"
                                    type="file"
                                    multiple
                                    @change="handleFileChange"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
                                />
                                <InputError class="mt-2" :message="form.errors.archivos" />
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center gap-4 border-t border-gray-100 pt-4">
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
