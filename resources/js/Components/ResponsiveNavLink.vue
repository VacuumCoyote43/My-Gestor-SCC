<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
    method: {
        type: String,
        default: 'get',
    },
    as: {
        type: String,
        default: 'a',
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full rounded-lg bg-primary-50 px-3 py-2.5 text-start text-sm font-semibold text-primary-700 shadow-sm ring-1 ring-primary-100 transition'
        : 'block w-full rounded-lg px-3 py-2.5 text-start text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900',
);
</script>

<template>
    <Link :href="href" :method="method" :as="as" :class="classes">
        <slot />
    </Link>
</template>
