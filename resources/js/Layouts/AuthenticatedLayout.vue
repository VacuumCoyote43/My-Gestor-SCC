<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const showingSidebar = ref(false);
const page = usePage();

const isImpersonating = computed(() => {
    return page.props.auth?.is_impersonating || false;
});

const showBackButton = computed(() => {
    const component = page.component || '';
    return !component.endsWith('/Index') && !component.endsWith('Dashboard');
});

const goBack = () => {
    window.history.back();
};

const userRole = computed(() => {
    return page.props.auth?.user?.role?.nombre || '';
});

const stopImpersonating = () => {
    router.post(route('stop-impersonating'));
};
</script>

<template>
    <div>
        <FlashMessages />
        
        <div class="min-h-screen bg-gray-50">
            <!-- Banner de impersonación -->
            <div class="flex min-h-screen">
                <!-- Mobile Sidebar Overlay -->
                <div v-if="showingSidebar" class="fixed inset-0 z-40 bg-black/40 lg:hidden" @click="showingSidebar = false"></div>

                <!-- Mobile Sidebar -->
                <aside
                    v-if="showingSidebar"
                    class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-xl lg:hidden"
                >
                    <div class="flex h-16 items-center border-b border-gray-200 px-6">
                        <Link :href="route('dashboard')" class="flex items-center gap-3">
                            <ApplicationLogo class="h-9 w-auto fill-current text-primary-600" />
                            <span class="text-sm font-semibold text-gray-900">Panel</span>
                        </Link>
                    </div>
                    <nav class="flex flex-col px-4 py-6" style="height: calc(100vh - 64px);">
                        <div class="space-y-6">
                            <div>
                                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-400">General</p>
                                <div class="space-y-1">
                                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                        Dashboard
                                    </NavLink>
                                    <NavLink :href="route('incidencias.index')" :active="route().current('incidencias.*')">
                                        Incidencias
                                    </NavLink>
                                </div>
                            </div>

                            <div v-if="userRole === 'admin'">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Administración</p>
                                <div class="space-y-1">
                                    <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')">
                                        Usuarios
                                    </NavLink>
                                    <NavLink :href="route('admin.clubes.index')" :active="route().current('admin.clubes.*')">
                                        Clubes
                                    </NavLink>
                                    <NavLink :href="route('admin.proveedores.index')" :active="route().current('admin.proveedores.*')">
                                        Proveedores
                                    </NavLink>
                                </div>
                            </div>

                            <div v-if="userRole === 'proveedor'">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Proveedor</p>
                                <div class="space-y-1">
                                    <NavLink :href="route('proveedor.facturas.index')" :active="route().current('proveedor.facturas.*')">
                                        Facturas
                                    </NavLink>
                                </div>
                            </div>

                            <div v-if="userRole === 'gestor_club'">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Gestión Club</p>
                                <div class="space-y-1">
                                    <NavLink :href="route('club.facturas.index')" :active="route().current('club.facturas.*')">
                                        Facturas Emitidas
                                    </NavLink>
                                    <NavLink :href="route('club.facturas-recibidas.index')" :active="route().current('club.facturas-recibidas.*')">
                                        Facturas Recibidas
                                    </NavLink>
                                    <NavLink :href="route('club.cargos.index')" :active="route().current('club.cargos.*')">
                                        Cargos Jugadores
                                    </NavLink>
                                </div>
                            </div>

                            <div v-if="userRole === 'jugador'">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Jugador</p>
                                <div class="space-y-1">
                                    <NavLink :href="route('jugador.cargos.index')" :active="route().current('jugador.cargos.*')">
                                        Mis Cargos
                                    </NavLink>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto border-t border-gray-200 pt-4">
                            <div class="px-2 text-sm font-medium text-gray-900">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="px-2 text-xs text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('profile.edit')">
                                    Perfil
                                </ResponsiveNavLink>
                                <ResponsiveNavLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                >
                                    Cerrar sesión
                                </ResponsiveNavLink>
                            </div>
                        </div>
                    </nav>
                </aside>

                <!-- Desktop Sidebar -->
                <aside class="hidden w-72 flex-col border-r border-gray-200 bg-white lg:flex">
                    <div class="flex h-16 items-center border-b border-gray-200 px-6">
                        <Link :href="route('dashboard')" class="flex items-center gap-3">
                            <ApplicationLogo class="h-9 w-auto fill-current text-primary-600" />
                            <span class="text-sm font-semibold text-gray-900">Panel</span>
                        </Link>
                    </div>
                    <nav class="flex h-full flex-col px-4 py-6">
                        <div class="space-y-6">
                            <div>
                                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-400">General</p>
                                <div class="space-y-1">
                                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                        Dashboard
                                    </NavLink>
                                    <NavLink :href="route('incidencias.index')" :active="route().current('incidencias.*')">
                                        Incidencias
                                    </NavLink>
                                </div>
                            </div>

                            <div v-if="userRole === 'admin'">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Administración</p>
                                <div class="space-y-1">
                                    <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')">
                                        Usuarios
                                    </NavLink>
                                    <NavLink :href="route('admin.clubes.index')" :active="route().current('admin.clubes.*')">
                                        Clubes
                                    </NavLink>
                                    <NavLink :href="route('admin.proveedores.index')" :active="route().current('admin.proveedores.*')">
                                        Proveedores
                                    </NavLink>
                                </div>
                            </div>

                            <div v-if="userRole === 'proveedor'">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Proveedor</p>
                                <div class="space-y-1">
                                    <NavLink :href="route('proveedor.facturas.index')" :active="route().current('proveedor.facturas.*')">
                                        Facturas
                                    </NavLink>
                                </div>
                            </div>

                            <div v-if="userRole === 'gestor_club'">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Gestión Club</p>
                                <div class="space-y-1">
                                    <NavLink :href="route('club.facturas.index')" :active="route().current('club.facturas.*')">
                                        Facturas Emitidas
                                    </NavLink>
                                    <NavLink :href="route('club.facturas-recibidas.index')" :active="route().current('club.facturas-recibidas.*')">
                                        Facturas Recibidas
                                    </NavLink>
                                    <NavLink :href="route('club.cargos.index')" :active="route().current('club.cargos.*')">
                                        Cargos Jugadores
                                    </NavLink>
                                </div>
                            </div>

                            <div v-if="userRole === 'jugador'">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-400">Jugador</p>
                                <div class="space-y-1">
                                    <NavLink :href="route('jugador.cargos.index')" :active="route().current('jugador.cargos.*')">
                                        Mis Cargos
                                    </NavLink>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto border-t border-gray-200 pt-4">
                            <div class="px-2 text-sm font-medium text-gray-900">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="px-2 text-xs text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('profile.edit')">
                                    Perfil
                                </ResponsiveNavLink>
                                <ResponsiveNavLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                >
                                    Cerrar sesión
                                </ResponsiveNavLink>
                            </div>
                        </div>
                    </nav>
                </aside>

                <div class="flex min-w-0 flex-1 flex-col">
                    <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/90 backdrop-blur lg:hidden">
                        <div class="flex h-16 items-center justify-between px-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2 text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-gray-900"
                                    @click="showingSidebar = true"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-widest text-gray-400">Panel</p>
                                    <p class="text-sm font-semibold text-gray-900">Gestor SCC</p>
                                </div>
                            </div>
                            <div v-if="showBackButton" class="ml-auto">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:text-gray-900"
                                    @click="goBack"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Volver
                                </button>
                            </div>
                        </div>
                    </header>

                    <header v-if="$slots.header" class="border-b border-gray-200 bg-white">
                        <div class="mx-auto w-full px-4 py-6 sm:px-6 lg:px-8 flex items-center justify-between">
                            <slot name="header" />
                            <!-- Solo mostrar si es vista desktop -->
                            <div v-if="showBackButton && !showingSidebar" class="ml-auto">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:text-gray-900"
                                    @click="goBack"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Volver
                                </button>
                            </div>
                        </div>
                    </header>
                    <div v-if="isImpersonating" class="bg-yellow-500 text-white px-4 py-2 text-center">
                        <span class="font-semibold">Estás viendo la aplicación como otro usuario.</span>
                        <button @click="stopImpersonating" class="ml-4 underline hover:no-underline">
                            Volver a mi cuenta de administrador
                        </button>
                    </div>

                    <main class="flex-1 bg-gray-50 p-4 sm:p-6 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700">
                        <slot />
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
