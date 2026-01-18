<script setup>
defineProps({
    title: String,
    value: [String, Number],
    icon: String,
    color: {
        type: String,
        default: 'blue',
    },
    trend: String,
    trendValue: String,
});

const colorClasses = {
    blue: 'from-blue-500 to-blue-600',
    green: 'from-green-500 to-green-600',
    red: 'from-red-500 to-red-600',
    yellow: 'from-yellow-500 to-yellow-600',
    purple: 'from-purple-500 to-purple-600',
    indigo: 'from-indigo-500 to-indigo-600',
};
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-200 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600 mb-1">{{ title }}</p>
                    <p class="text-3xl font-bold text-gray-900">{{ value }}</p>
                    <div v-if="trend" class="flex items-center mt-2">
                        <span :class="trend === 'up' ? 'text-green-600' : 'text-red-600'" class="text-sm font-medium">
                            {{ trend === 'up' ? '↑' : '↓' }} {{ trendValue }}
                        </span>
                    </div>
                </div>
                <div v-if="icon" :class="`bg-gradient-to-br ${colorClasses[color]}`" class="w-16 h-16 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-3xl">{{ icon }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
