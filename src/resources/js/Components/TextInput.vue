<script setup>
import { onMounted, ref, useAttrs } from 'vue';

const model = defineModel({
    type: String,
    required: false,
});

const props = defineProps({
    type: {
        type: String,
        default: 'text',
    },
});

const attrs = useAttrs();
const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        :type="type"
        class="block w-full rounded-lg border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all duration-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-primary-400 dark:focus:ring-primary-400/20"
        v-model="model"
        v-bind="$attrs"
        ref="input"
    />
</template>
