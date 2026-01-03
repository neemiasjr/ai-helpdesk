<script setup>
import { computed } from 'vue';

const props = defineProps({
    error: {
        type: String,
        default: null,
    },
});

const isMissingApiKey = computed(() => {
    return props.error?.includes('AI_API_KEY') || 
           props.error?.includes('não configurada') ||
           props.error?.includes('não está definida');
});

const errorMessage = computed(() => {
    if (isMissingApiKey.value) {
        return 'Serviço de IA não configurado. Configure a chave de API (AI_API_KEY) no arquivo .env para usar os recursos de Inteligência Artificial.';
    }
    return props.error || 'Ocorreu um erro ao processar a solicitação de IA.';
});
</script>

<template>
    <div
        v-if="error"
        class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20"
    >
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg
                    class="h-5 w-5 text-red-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-semibold text-red-800 dark:text-red-300">
                    Erro na Integração de IA
                </h3>
                <div class="mt-2 text-sm text-red-700 dark:text-red-400">
                    {{ errorMessage }}
                </div>
                <div
                    v-if="isMissingApiKey"
                    class="mt-3 rounded-lg bg-red-100 p-3 dark:bg-red-900/30"
                >
                    <p class="text-xs font-medium text-red-900 dark:text-red-200">
                        Como configurar:
                    </p>
                    <ol class="mt-1 list-inside list-decimal space-y-1 text-xs text-red-800 dark:text-red-300">
                        <li>Adicione <code class="rounded bg-red-200 px-1 py-0.5 dark:bg-red-800">AI_API_KEY=sk-...</code> no arquivo <code class="rounded bg-red-200 px-1 py-0.5 dark:bg-red-800">.env</code></li>
                        <li>Execute <code class="rounded bg-red-200 px-1 py-0.5 dark:bg-red-800">php artisan config:clear</code></li>
                        <li>Recarregue a página</li>
                    </ol>
                </div>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button
                    @click="$emit('dismiss')"
                    class="inline-flex rounded-md text-red-400 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:hover:text-red-300"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

