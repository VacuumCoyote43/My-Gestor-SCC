<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({}),
    },
    routeName: {
        type: String,
        required: true,
    },
    placeholder: {
        type: String,
        default: 'Buscar...',
    },
    showFilters: {
        type: Boolean,
        default: false,
    },
});

const search = ref(props.filters.search || '');
const showAdvancedFilters = ref(false);

const emit = defineEmits(['filter-change']);

watch(search, (value) => {
    performSearch();
});

const performSearch = () => {
    router.get(route(props.routeName), {
        search: search.value,
        ...props.filters,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearSearch = () => {
    search.value = '';
    router.get(route(props.routeName), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Buscador principal -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    v-model="search"
                    type="text"
                    :placeholder="placeholder"
                    class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-all duration-200"
                />
                <div v-if="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button
                        @click="clearSearch"
                        type="button"
                        class="text-gray-400 hover:text-gray-600 focus:outline-none"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Botón de filtros avanzados -->
            <button
                v-if="showFilters"
                @click="showAdvancedFilters = !showAdvancedFilters"
                type="button"
                class="inline-flex items-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200"
            >
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filtros
            </button>
        </div>

        <!-- Filtros avanzados -->
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div v-if="showAdvancedFilters && showFilters" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <slot name="filters" />
            </div>
        </transition>
    </div>
</template>
