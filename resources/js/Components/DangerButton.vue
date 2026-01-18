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
        class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-red-500/50 transition-all duration-200 hover:from-red-700 hover:to-red-800 hover:shadow-xl hover:shadow-red-500/50 focus:outline-none focus:ring-4 focus:ring-red-300 active:scale-95"
    >
        <slot />
    </button>
</template>
