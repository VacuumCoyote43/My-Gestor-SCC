<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head title="Bienvenido - My Gestor SCC" />
    
    <div class="min-h-screen bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700">
        <!-- Navegación -->
        <nav class="relative z-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-20 items-center justify-between flex-col sm:flex-row sm:space-x-5 space-y-5">
                    <div class="flex items-center sm:mt-0 mt-5">
                        <div class="flex items-center space-x-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 backdrop-blur-sm">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">My Gestor</h1>
                                <p class="text-sm text-primary-200">Sistema de Gestión SCC</p>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="canLogin" class="flex items-center space-x-4 sm:space-x-4">
                        <Link
                            :href="route('login')"
                            class="rounded-lg px-6 py-2.5 text-sm font-medium text-white transition-all duration-200 hover:bg-white/10 backdrop-blur-sm"
                        >
                            Iniciar Sesión
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-lg bg-white px-6 py-2.5 text-sm font-medium text-primary-700 shadow-lg transition-all duration-200 hover:bg-primary-50 hover:shadow-xl active:scale-95"
                        >
                            Registrarse
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-primary-600/20 blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-primary-600/20 blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-32">
                <div class="text-center">
                    <h2 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl lg:text-7xl">
                        Gestión Económica
                        <span class="block text-primary-200">para Ligas de Fútbol</span>
                    </h2>
                    <p class="mx-auto mt-6 max-w-2xl text-xl text-primary-100">
                        Controla facturas, pagos, cargos a jugadores e incidencias de forma centralizada y profesional.
                    </p>
                    <div class="mt-10 flex justify-center gap-4">
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="inline-flex items-center justify-center rounded-lg bg-white px-8 py-4 text-lg font-semibold text-primary-700 shadow-2xl transition-all duration-200 hover:bg-primary-50 hover:shadow-3xl active:scale-95"
                        >
                            <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Acceder al Sistema
                        </Link>
                    </div>
                </div>

                <!-- Features Grid -->
                <div class="mt-24 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Feature 1 -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white/10 p-8 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 hover:shadow-2xl">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-white">Facturación</h3>
                        <p class="text-primary-100">Gestión completa de facturas entre proveedores, clubes y la liga.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white/10 p-8 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 hover:shadow-2xl">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-green-600 shadow-lg">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-white">Pagos</h3>
                        <p class="text-primary-100">Control de pagos con justificantes y validación manual.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white/10 p-8 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 hover:shadow-2xl">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-lg">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-white">Cargos Jugadores</h3>
                        <p class="text-primary-100">Gestión de deudas y cargos económicos a jugadores.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="group relative overflow-hidden rounded-2xl bg-white/10 p-8 backdrop-blur-sm transition-all duration-300 hover:bg-white/20 hover:shadow-2xl">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-red-600 shadow-lg">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-white">Incidencias</h3>
                        <p class="text-primary-100">Sistema de tickets con chat público e interno.</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-24 grid gap-8 sm:grid-cols-3">
                    <div class="text-center">
                        <div class="text-5xl font-bold text-white">100%</div>
                        <div class="mt-2 text-lg text-primary-200">Trazabilidad</div>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-bold text-white">24/7</div>
                        <div class="mt-2 text-lg text-primary-200">Disponibilidad</div>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-bold text-white">100%</div>
                        <div class="mt-2 text-lg text-primary-200">Seguro</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="relative border-t border-white/10 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-primary-200">
                    © {{ new Date().getFullYear() }} My Gestor SCC. Sistema de Gestión para Ligas de Fútbol.
                </p>
            </div>
        </footer>
    </div>
</template>
