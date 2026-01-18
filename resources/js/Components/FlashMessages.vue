<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref('success');

watch(() => page.props.flash, (flash) => {
    if (flash.success) {
        message.value = flash.success;
        type.value = 'success';
        show.value = true;
        setTimeout(() => show.value = false, 5000);
    } else if (flash.error) {
        message.value = flash.error;
        type.value = 'error';
        show.value = true;
        setTimeout(() => show.value = false, 5000);
    }
}, { deep: true, immediate: true });
</script>

<template>
    <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform translate-y-2"
    >
        <div
            v-if="show"
            :class="{
                'bg-green-50 border-green-500 text-green-800': type === 'success',
                'bg-red-50 border-red-500 text-red-800': type === 'error'
            }"
            class="fixed top-4 right-4 z-50 max-w-md p-4 border-l-4 rounded shadow-lg"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg v-if="type === 'success'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <svg v-if="type === 'error'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ message }}</span>
                </div>
                <button @click="show = false" class="ml-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </transition>
</template>
