<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
    incidencia: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAdmin = computed(() => user.value?.role?.nombre === 'admin');

const mensajeForm = useForm({
    mensaje: '',
    tipo: 'publico',
    archivos: [],
});

const estadoForm = useForm({
    estado: props.incidencia.estado,
});

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'abierta': 'bg-red-100 text-red-800',
        'en_progreso': 'bg-yellow-100 text-yellow-800',
        'cerrada': 'bg-green-100 text-green-800',
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};

const getPrioridadBadgeClass = (prioridad) => {
    const classes = {
        'baja': 'bg-gray-100 text-gray-800',
        'media': 'bg-blue-100 text-blue-800',
        'alta': 'bg-orange-100 text-orange-800',
        'urgente': 'bg-red-100 text-red-800',
    };
    return classes[prioridad] || 'bg-gray-100 text-gray-800';
};

const handleFileChange = (event) => {
    mensajeForm.archivos = Array.from(event.target.files);
};

const enviarMensaje = () => {
    mensajeForm.post(route('incidencias.mensajes.store', props.incidencia.id), {
        preserveScroll: true,
        onSuccess: () => {
            mensajeForm.reset();
            document.getElementById('archivos').value = '';
        },
    });
};

const cambiarEstado = () => {
    estadoForm.patch(route('incidencias.estado', props.incidencia.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Incidencia: ${incidencia.asunto}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-900">
                Incidencia: {{ incidencia.asunto }}
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Información de la incidencia -->
                <Card>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalles</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Creada por</dt>
                                        <dd class="text-sm text-gray-900">{{ incidencia.creador.name }} ({{ incidencia.creador.email }})</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                                        <dd class="text-sm text-gray-900">{{ incidencia.categoria }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Prioridad</dt>
                                        <dd>
                                            <span :class="getPrioridadBadgeClass(incidencia.prioridad)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ incidencia.prioridad }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Fecha Apertura</dt>
                                        <dd class="text-sm text-gray-900">{{ new Date(incidencia.fecha_apertura).toLocaleString('es-ES') }}</dd>
                                    </div>
                                    <div v-if="incidencia.fecha_cierre">
                                        <dt class="text-sm font-medium text-gray-500">Fecha Cierre</dt>
                                        <dd class="text-sm text-gray-900">{{ new Date(incidencia.fecha_cierre).toLocaleString('es-ES') }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div v-if="isAdmin">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Gestión (Admin)</h3>
                                <div>
                                    <InputLabel for="estado" value="Cambiar Estado" />
                                    <div class="flex gap-2 mt-1">
                                        <select
                                            id="estado"
                                            v-model="estadoForm.estado"
                                            class="block w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-200"
                                        >
                                            <option value="abierta">Abierta</option>
                                            <option value="en_progreso">En Progreso</option>
                                            <option value="cerrada">Cerrada</option>
                                        </select>
                                        <PrimaryButton @click="cambiarEstado" :disabled="estadoForm.processing">
                                            Actualizar
                                        </PrimaryButton>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <dt class="text-sm font-medium text-gray-500">Estado Actual</dt>
                                    <dd class="mt-1">
                                        <span :class="getEstadoBadgeClass(incidencia.estado)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ incidencia.estado }}
                                        </span>
                                    </dd>
                                </div>
                            </div>
                            <div v-else>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Estado</h3>
                                <span :class="getEstadoBadgeClass(incidencia.estado)" class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full">
                                    {{ incidencia.estado }}
                                </span>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Chat de mensajes -->
                <Card>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversación</h3>
                        
                        <!-- Mensajes -->
                        <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
                            <div v-for="mensaje in incidencia.mensajes" :key="mensaje.id" class="p-4 border rounded-lg" :class="mensaje.tipo === 'interno' ? 'bg-yellow-50 border-yellow-200' : 'bg-white border-gray-200'">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ mensaje.user.name }}</span>
                                        <span class="ml-2 text-xs text-gray-500">{{ new Date(mensaje.fecha_mensaje).toLocaleString('es-ES') }}</span>
                                        <span v-if="mensaje.tipo === 'interno'" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Interno (solo admin)
                                        </span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ mensaje.mensaje }}</p>
                                
                                <!-- Archivos adjuntos -->
                                <div v-if="mensaje.archivo_adjuntos && mensaje.archivo_adjuntos.length > 0" class="mt-3">
                                    <p class="text-xs text-gray-500 mb-1">Archivos adjuntos:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <a v-for="archivo in mensaje.archivo_adjuntos" :key="archivo.id" :href="route('archivos.download', archivo.id)" class="text-xs text-blue-600 hover:text-blue-900 underline">
                                            📎 {{ archivo.nombre_original }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div v-if="!incidencia.mensajes || incidencia.mensajes.length === 0" class="text-center text-gray-500 py-8">
                                No hay mensajes aún. ¡Sé el primero en comentar!
                            </div>
                        </div>

                        <!-- Formulario nuevo mensaje -->
                        <form @submit.prevent="enviarMensaje" class="space-y-4 border-t pt-4">
                            <div>
                                <InputLabel for="mensaje" value="Nuevo Mensaje" />
                                <textarea
                                    id="mensaje"
                                    v-model="mensajeForm.mensaje"
                                    rows="4"
                                    class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-200"
                                    placeholder="Escribe tu mensaje aquí..."
                                    required
                                ></textarea>
                                <InputError class="mt-2" :message="mensajeForm.errors.mensaje" />
                            </div>

                            <div v-if="isAdmin">
                                <InputLabel for="tipo" value="Tipo de Mensaje" />
                                <select
                                    id="tipo"
                                    v-model="mensajeForm.tipo"
                                    class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-primary-400 focus:bg-white focus:ring-2 focus:ring-primary-200"
                                >
                                    <option value="publico">Público (visible para el creador)</option>
                                    <option value="interno">Interno (solo admins)</option>
                                </select>
                                <InputError class="mt-2" :message="mensajeForm.errors.tipo" />
                            </div>

                            <div>
                                <InputLabel for="archivos" value="Archivos Adjuntos (máx. 6MB cada uno)" />
                                <input
                                    id="archivos"
                                    type="file"
                                    multiple
                                    @change="handleFileChange"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
                                />
                                <InputError class="mt-2" :message="mensajeForm.errors.archivos" />
                            </div>

                            <div class="flex items-center gap-4 border-t border-gray-100 pt-4">
                                <PrimaryButton :disabled="mensajeForm.processing">
                                    Enviar Mensaje
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
