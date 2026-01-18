<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    clubes: Array,
    jugadores: Array,
});

const form = useForm({
    club_id: null,
    user_id_jugador: null,
    fecha_emision: new Date().toISOString().split('T')[0],
    fecha_vencimiento: null,
    conceptos: [
        { descripcion: '', total_linea: 0 }
    ],
});

const agregarConcepto = () => {
    form.conceptos.push({ descripcion: '', total_linea: 0 });
};

const eliminarConcepto = (index) => {
    if (form.conceptos.length > 1) {
        form.conceptos.splice(index, 1);
    }
};

const submit = () => {
    form.post(route('club.cargos.store'));
};
</script>

<template>
    <Head title="Nuevo Cargo a Jugador" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Nuevo Cargo a Jugador
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Datos básicos -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <InputLabel for="club_id" value="Club *" />
                                    <select
                                        id="club_id"
                                        v-model="form.club_id"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option :value="null">Seleccione un club...</option>
                                        <option v-for="club in clubes" :key="club.id" :value="club.id">
                                            {{ club.nombre }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.club_id" />
                                </div>

                                <div>
                                    <InputLabel for="user_id_jugador" value="Jugador *" />
                                    <select
                                        id="user_id_jugador"
                                        v-model="form.user_id_jugador"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option :value="null">Seleccione un jugador...</option>
                                        <option v-for="jugador in jugadores" :key="jugador.id" :value="jugador.id">
                                            {{ jugador.name }} ({{ jugador.email }})
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.user_id_jugador" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <InputLabel for="fecha_emision" value="Fecha Emisión *" />
                                    <TextInput
                                        id="fecha_emision"
                                        v-model="form.fecha_emision"
                                        type="date"
                                        class="block w-full mt-1"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.fecha_emision" />
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

                            <!-- Conceptos -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Conceptos del Cargo</h3>
                                    <SecondaryButton type="button" @click="agregarConcepto">
                                        + Agregar Concepto
                                    </SecondaryButton>
                                </div>

                                <div v-for="(concepto, index) in form.conceptos" :key="index" class="p-4 mb-4 border border-gray-200 rounded-lg">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <InputLabel :for="`descripcion_${index}`" value="Descripción *" />
                                            <TextInput
                                                :id="`descripcion_${index}`"
                                                v-model="concepto.descripcion"
                                                type="text"
                                                class="block w-full mt-1"
                                                placeholder="Ej: Ficha federativa, Seguro, Cuota mensual..."
                                                required
                                            />
                                        </div>

                                        <div>
                                            <InputLabel :for="`total_${index}`" value="Importe *" />
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
                                            class="text-sm text-red-600 hover:text-red-900"
                                        >
                                            Eliminar concepto
                                        </button>
                                    </div>
                                </div>

                                <InputError class="mt-2" :message="form.errors.conceptos" />
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    Crear Cargo (Borrador)
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
