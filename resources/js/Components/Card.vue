<script setup>
defineProps({
    padding: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="rounded-2xl border border-gray-200/80 bg-white shadow-sm shadow-gray-200/50 ring-1 ring-white/60 transition hover:-translate-y-0.5 hover:shadow-md">
        <div :class="padding ? 'p-6' : ''">
            <slot />
        </div>
    </div>
</template>
