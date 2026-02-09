<script setup>
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
        // Format: { key: 'id', label: 'ID', sortable: true, class: 'text-left' }
    },
    stickyHeader: {
        type: Boolean,
        default: false,
    },
    sortable: {
        type: Boolean,
        default: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    routeName: {
        type: String,
        required: true,
    },
});

const primaryColumn = computed(() => props.columns.find((col) => col.primary) || props.columns[0]);
const detailColumns = computed(() =>
    primaryColumn.value ? props.columns.filter((col) => col.key !== primaryColumn.value.key) : props.columns,
);

const sort = (key) => {
    if (!props.sortable) return;

    let sort = props.filters.sort;
    let direction = props.filters.direction;

    if (sort === key) {
        direction = direction === 'asc' ? 'desc' : 'asc';
    } else {
        sort = key;
        direction = 'asc';
    }

    router.get(route(props.routeName), {
        ...props.filters,
        sort: sort,
        direction: direction,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};
</script>

<template>
    <div class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm shadow-gray-200/50">
        <div class="md:hidden">
            <div v-if="rows.data.length === 0" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center text-gray-500">
                    <svg class="mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-base font-semibold text-gray-600">No se encontraron resultados</span>
                    <span class="text-sm text-gray-500">Intenta ajustar tus filtros o búsqueda.</span>
                </div>
            </div>
            <div v-else class="divide-y divide-gray-200">
                <article
                    v-for="(row, index) in rows.data"
                    :key="row.id || index"
                    class="space-y-3 px-4 py-5"
                >
                    <div v-if="primaryColumn" class="text-base font-semibold text-gray-900">
                        <slot :name="`cell-${primaryColumn.key}`" :row="row" :value="row[primaryColumn.key]">
                            {{ row[primaryColumn.key] }}
                        </slot>
                    </div>
                    <div class="space-y-2 text-sm text-gray-700">
                        <div
                            v-for="col in detailColumns"
                            :key="col.key"
                            class="flex items-start justify-between gap-4"
                        >
                            <span class="text-xs font-semibold uppercase tracking-widest text-gray-400">{{ col.label }}</span>
                            <span class="text-right text-gray-900">
                                <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                    {{ row[col.key] }}
                                </slot>
                            </span>
                        </div>
                    </div>
                </article>
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead :class="['bg-gray-50', stickyHeader ? 'sticky top-0 z-10 shadow-sm' : '']">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            scope="col"
                            class="px-6 py-3 text-xs font-semibold tracking-widest text-left text-gray-500 uppercase transition-colors duration-200"
                            :class="[
                                col.class || '',
                                { 'cursor-pointer hover:text-gray-700': col.sortable }
                            ]"
                            @click="col.sortable && sort(col.key)"
                        >
                            <div class="flex items-center gap-1">
                                <span>{{ col.label }}</span>
                                <span v-if="col.sortable && filters.sort === col.key" class="text-primary-500">
                                    <svg v-if="filters.direction === 'asc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </span>
                                <span v-if="col.sortable && filters.sort !== col.key" class="text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="(row, index) in rows.data" :key="row.id || index" class="transition-colors duration-150 hover:bg-gray-50">
                        <td
                            v-for="col in columns"
                            :key="col.key"
                            class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap"
                            :class="col.rowClass || ''"
                        >
                            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                {{ row[col.key] }}
                            </slot>
                        </td>
                    </tr>
                    <tr v-if="rows.data.length === 0">
                        <td :colspan="columns.length" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <svg class="mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-base font-semibold text-gray-600">No se encontraron resultados</span>
                                <span class="text-sm text-gray-500">Intenta ajustar tus filtros o búsqueda.</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div v-if="rows.meta || rows.links" class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            <slot name="pagination" :links="rows.links" :meta="rows.meta">
                <!-- Fallback to a simple implementation if not provided via slot, though we should likely use a dedicated component -->
            </slot>
        </div>
    </div>
</template>
