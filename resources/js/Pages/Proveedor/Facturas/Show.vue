<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    factura: Object,
});

const emitForm = useForm({});
const validateForm = useForm({ comentario: '' });
const rejectForm = useForm({ comentario: '' });

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
};

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'draft': 'bg-gray-100 text-gray-800',
        'pending_payment': 'bg-yellow-100 text-yellow-800',
        'payment_registered': 'bg-blue-100 text-blue-800',
        'paid': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};

const emitirFactura = () => {
    if (confirm('¿Está seguro de emitir esta factura? Una vez emitida no podrá modificarse.')) {
        emitForm.post(route('proveedor.facturas.emit', props.factura.id));
    }
};

const validarPago = (pagoId) => {
    const comentario = prompt('Comentario (opcional):');
    validateForm.comentario = comentario || '';
    validateForm.post(route('proveedor.pagos.validate', pagoId));
};

const rechazarPago = (pagoId) => {
    const comentario = prompt('Motivo del rechazo (obligatorio):');
    if (comentario) {
        rejectForm.comentario = comentario;
        rejectForm.post(route('proveedor.pagos.reject', pagoId));
    }
};
</script>

<template>
    <Head title="Detalle Factura" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Factura {{ factura.numero }}
                </h2>
                <div class="flex gap-2">
                    <Link v-if="factura.estado === 'draft'" :href="route('proveedor.facturas.edit', factura.id)" class="inline-flex items-center px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Editar
                    </Link>
                    <PrimaryButton v-if="factura.estado === 'draft'" @click="emitirFactura" :disabled="emitForm.processing">
                        Emitir Factura
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Información básica -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información de la Factura</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Número</dt>
                                        <dd class="text-sm text-gray-900">{{ factura.numero }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Serie</dt>
                                        <dd class="text-sm text-gray-900">{{ factura.serie }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Fecha Factura</dt>
                                        <dd class="text-sm text-gray-900">{{ new Date(factura.fecha_factura).toLocaleDateString('es-ES') }}</dd>
                                    </div>
                                    <div v-if="factura.fecha_vencimiento">
                                        <dt class="text-sm font-medium text-gray-500">Fecha Vencimiento</dt>
                                        <dd class="text-sm text-gray-900">{{ new Date(factura.fecha_vencimiento).toLocaleDateString('es-ES') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                        <dd>
                                            <span :class="getEstadoBadgeClass(factura.estado)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ factura.estado }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Receptor</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                                        <dd class="text-sm text-gray-900">{{ factura.receptor?.nombre || factura.receptor?.nombre_legal }}</dd>
                                    </div>
                                    <div v-if="factura.receptor?.email">
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="text-sm text-gray-900">{{ factura.receptor.email }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conceptos -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Conceptos</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Descripción</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Cantidad</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Precio Unit.</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">% Impuesto</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="concepto in factura.conceptos" :key="concepto.id">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ concepto.descripcion }}</td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-500">{{ concepto.cantidad }}</td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-500">{{ formatCurrency(concepto.precio_unitario) }}</td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-500">{{ concepto.tipo_impuesto }}%</td>
                                        <td class="px-6 py-4 text-sm font-medium text-right text-gray-900">{{ formatCurrency(concepto.total_linea) }}</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td colspan="4" class="px-6 py-4 text-sm font-semibold text-right text-gray-900">Total Factura</td>
                                        <td class="px-6 py-4 text-lg font-bold text-right text-gray-900">{{ formatCurrency(factura.total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Documentos Adjuntos -->
                <div v-if="factura.archivo_adjuntos?.length > 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Documentos Adjuntos</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div v-for="archivo in factura.archivo_adjuntos" :key="archivo.id" class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" :title="archivo.nombre_original">
                                            📄 {{ archivo.nombre_original }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ (archivo.tamano / 1024).toFixed(2) }} KB
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2 mt-3">
                                    <a :href="route('archivos.view', archivo.id)" target="_blank" class="text-xs text-blue-600 hover:text-blue-800">
                                        Ver
                                    </a>
                                    <a :href="route('archivos.download', archivo.id)" class="text-xs text-green-600 hover:text-green-800">
                                        Descargar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagos -->
                <div v-if="factura.pagos && factura.pagos.length > 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pagos Registrados</h3>
                        <div class="space-y-4">
                            <div v-for="pago in factura.pagos" :key="pago.id" class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ formatCurrency(pago.importe) }}</span>
                                        <span class="ml-2 text-sm text-gray-500">{{ new Date(pago.fecha_pago).toLocaleDateString('es-ES') }}</span>
                                        <span class="ml-2 text-sm text-gray-500">{{ pago.metodo_pago }}</span>
                                    </div>
                                    <span :class="pago.estado_pago === 'validado' ? 'bg-green-100 text-green-800' : pago.estado_pago === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ pago.estado_pago }}
                                    </span>
                                </div>
                                <div v-if="pago.estado_pago === 'registrado'" class="flex gap-2 mt-2">
                                    <button @click="validarPago(pago.id)" class="px-3 py-1 text-sm text-white bg-green-600 rounded hover:bg-green-700">
                                        Validar
                                    </button>
                                    <button @click="rechazarPago(pago.id)" class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                                        Rechazar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
