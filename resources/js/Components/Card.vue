<script setup>
defineProps({
    padding: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
        <div :class="padding ? 'p-6' : ''">
            <slot />
        </div>
    </div>
</template>
