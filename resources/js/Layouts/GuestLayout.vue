<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const showBackButton = computed(() => {
    const component = page.component || '';
    return !component.endsWith('/Index');
});

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div class="mt-6 sm:mt-0 overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg rounded-xl">
            <slot />
        </div>
    </div>
</template>
