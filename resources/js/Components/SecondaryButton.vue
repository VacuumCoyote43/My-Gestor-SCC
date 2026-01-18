<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 hover:border-gray-400 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-gray-200 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
    >
        <slot />
    </button>
</template>
