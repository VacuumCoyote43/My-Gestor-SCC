<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    cargo: Object,
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

const emitirCargo = () => {
    if (confirm('¿Está seguro de emitir este cargo? Se notificará al jugador por email.')) {
        emitForm.post(route('club.cargos.emit', props.cargo.id));
    }
};

const validarPago = (pagoId) => {
    const comentario = prompt('Comentario (opcional):');
    validateForm.comentario = comentario || '';
    validateForm.post(route('club.pagos.validate', pagoId));
};

const rechazarPago = (pagoId) => {
    const comentario = prompt('Motivo del rechazo (obligatorio):');
    if (comentario) {
        rejectForm.comentario = comentario;
        rejectForm.post(route('club.pagos.reject', pagoId));
    }
};
</script>

<template>
    <Head title="Detalle Cargo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Cargo #{{ cargo.id }}
                </h2>
                <div class="flex gap-2">
                    <PrimaryButton v-if="cargo.estado === 'draft'" @click="emitirCargo" :disabled="emitForm.processing">
                        Emitir Cargo
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
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Cargo</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Club</dt>
                                        <dd class="text-sm text-gray-900">{{ cargo.club.nombre }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Jugador</dt>
                                        <dd class="text-sm text-gray-900">{{ cargo.jugador.name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email Jugador</dt>
                                        <dd class="text-sm text-gray-900">{{ cargo.jugador.email }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Fecha Emisión</dt>
                                        <dd class="text-sm text-gray-900">{{ new Date(cargo.fecha_emision).toLocaleDateString('es-ES') }}</dd>
                                    </div>
                                    <div v-if="cargo.fecha_vencimiento">
                                        <dt class="text-sm font-medium text-gray-500">Fecha Vencimiento</dt>
                                        <dd class="text-sm text-gray-900">{{ new Date(cargo.fecha_vencimiento).toLocaleDateString('es-ES') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                        <dd>
                                            <span :class="getEstadoBadgeClass(cargo.estado)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ cargo.estado }}
                                            </span>
                                        </dd>
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
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Importe</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="concepto in cargo.conceptos" :key="concepto.id">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ concepto.descripcion }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-right text-gray-900">{{ formatCurrency(concepto.total_linea) }}</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-semibold text-right text-gray-900">Total Cargo</td>
                                        <td class="px-6 py-4 text-lg font-bold text-right text-gray-900">{{ formatCurrency(cargo.total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagos -->
                <div v-if="cargo.pagos && cargo.pagos.length > 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pagos Registrados por el Jugador</h3>
                        <div class="space-y-4">
                            <div v-for="pago in cargo.pagos" :key="pago.id" class="p-4 border border-gray-200 rounded-lg">
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
                                <div v-if="pago.archivo_adjuntos && pago.archivo_adjuntos.length > 0" class="mt-2">
                                    <p class="text-xs text-gray-500 mb-1">Justificantes:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <a v-for="archivo in pago.archivo_adjuntos" :key="archivo.id" :href="route('archivos.download', archivo.id)" class="text-xs text-blue-600 hover:text-blue-900">
                                            {{ archivo.nombre_original }}
                                        </a>
                                    </div>
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
