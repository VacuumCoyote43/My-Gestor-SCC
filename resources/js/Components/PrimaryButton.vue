<script setup>
defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        :class="{
            'opacity-50 cursor-not-allowed': disabled,
        }"
        :disabled="disabled"
        class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-primary-500/50 transition-all duration-200 hover:from-primary-700 hover:to-primary-800 hover:shadow-xl hover:shadow-primary-500/50 focus:outline-none focus:ring-4 focus:ring-primary-300 active:scale-95"
    >
        <slot />
    </button>
</template>
