<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import AiErrorAlert from '@/Components/AiErrorAlert.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';

const page = usePage();
const ticket = computed(() => page.props.ticket);
const can = computed(() => page.props.can);

const commentForm = useForm({
    body: '',
});

const aiError = ref(null);

function addComment() {
    commentForm.post(route('tickets.comments.store', ticket.value.id), {
        preserveScroll: true,
        onSuccess: () => commentForm.reset('body'),
    });
}

function runAi(action) {
    aiError.value = null; // Clear previous errors
    
    const map = {
        summarize: route('tickets.ai.summarize', ticket.value.id),
        classify: route('tickets.ai.classify', ticket.value.id),
        suggestReply: route('tickets.ai.suggestReply', ticket.value.id),
    };
    
    router.post(map[action], {}, {
        preserveScroll: true,
        onError: (errors) => {
            // Handle validation errors
            const errorMessage = errors.ai_config || errors.message || 'Erro ao processar solicitação de IA';
            aiError.value = errorMessage;
        },
        onFinish: () => {
            // Check for flash messages
            if (page.props.flash?.error) {
                aiError.value = page.props.flash.error;
            }
        },
    });
}

function dismissError() {
    aiError.value = null;
}

// Check for errors on mount
onMounted(() => {
    if (page.props.flash?.error) {
        aiError.value = page.props.flash.error;
    }
    
    // Check for errors in page props
    if (page.props.errors?.ai_config) {
        aiError.value = page.props.errors.ai_config;
    }
});

function getStatusColor(status) {
    const colors = {
        open: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-800',
        in_progress: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800',
        resolved: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-800',
        closed: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600',
    };
    return colors[status] || colors.open;
}

function getStatusLabel(status) {
    const labels = {
        open: 'Aberto',
        in_progress: 'Em Progresso',
        resolved: 'Resolvido',
        closed: 'Fechado',
    };
    return labels[status] || status;
}

function getPriorityColor(priority) {
    const colors = {
        low: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600',
        medium: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-800',
        high: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300 border-orange-200 dark:border-orange-800',
        urgent: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 border-red-200 dark:border-red-800',
    };
    return colors[priority] || colors.medium;
}

function getPriorityLabel(priority) {
    const labels = {
        low: 'Baixa',
        medium: 'Média',
        high: 'Alta',
        urgent: 'Urgente',
    };
    return labels[priority] || priority;
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('pt-PT', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

function formatDateTime(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'há alguns segundos';
    if (diffMins < 60) return `há ${diffMins} minuto${diffMins > 1 ? 's' : ''}`;
    if (diffHours < 24) return `há ${diffHours} hora${diffHours > 1 ? 's' : ''}`;
    if (diffDays < 7) return `há ${diffDays} dia${diffDays > 1 ? 's' : ''}`;
    return formatDate(dateString);
}
</script>

<template>
    <Head :title="ticket.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('tickets.index')"
                        class="flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                        <span class="ml-1 text-sm font-medium">Voltar</span>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">
                            Ticket #{{ ticket.id }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Criado em {{ formatDate(ticket.created_at) }}
                        </p>
                    </div>
                </div>
                <div v-if="can.update">
                    <Link
                        :href="route('tickets.edit', ticket.id)"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200"
                    >
                        <svg
                            class="w-4 h-4 mr-2"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                        Editar
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Conteúdo Principal -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Ticket Header -->
                        <div
                            class="overflow-hidden rounded-2xl bg-white shadow-lg dark:bg-gray-800 border border-gray-100 dark:border-gray-700"
                        >
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ ticket.title }}
                                    </h1>
                                </div>

                                <div class="flex flex-wrap items-center gap-3 mb-6">
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold border"
                                        :class="getStatusColor(ticket.status)"
                                    >
                                        {{ getStatusLabel(ticket.status) }}
                                    </span>
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold border"
                                        :class="getPriorityColor(ticket.priority)"
                                    >
                                        {{ getPriorityLabel(ticket.priority) }}
                                    </span>
                                    <span
                                        v-if="ticket.category"
                                        class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600"
                                    >
                                        {{ ticket.category }}
                                    </span>
                                </div>

                                <div
                                    class="prose prose-sm max-w-none dark:prose-invert text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed"
                                >
                                    {{ ticket.description }}
                                </div>

                                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Criado por:</span>
                                            <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">
                                                {{ ticket.creator?.name || 'N/A' }}
                                            </span>
                                        </div>
                                        <div v-if="ticket.assignee">
                                            <span class="text-gray-500 dark:text-gray-400">Atribuído a:</span>
                                            <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">
                                                {{ ticket.assignee?.name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comentários -->
                        <div
                            class="overflow-hidden rounded-2xl bg-white shadow-lg dark:bg-gray-800 border border-gray-100 dark:border-gray-700"
                        >
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                                    Comentários
                                    <span
                                        v-if="ticket.comments?.length"
                                        class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400"
                                    >
                                        ({{ ticket.comments.length }})
                                    </span>
                                </h3>

                                <div
                                    v-if="ticket.comments && ticket.comments.length > 0"
                                    class="space-y-4 mb-6"
                                >
                                    <div
                                        v-for="comment in ticket.comments"
                                        :key="comment.id"
                                        class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 transition-all duration-200 hover:shadow-md"
                                    >
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-primary-500 to-primary-600 text-sm font-semibold text-white"
                                                >
                                                    {{ comment.author?.name?.charAt(0)?.toUpperCase() || 'U' }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ comment.author?.name || 'Usuário' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ formatDateTime(comment.created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed ml-13"
                                        >
                                            {{ comment.body }}
                                        </div>
                                    </div>
                                </div>

                                <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    Nenhum comentário ainda. Seja o primeiro a comentar!
                                </div>

                                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <form @submit.prevent="addComment" class="space-y-4">
                                        <div>
                                            <textarea
                                                v-model="commentForm.body"
                                                rows="4"
                                                class="block w-full rounded-lg border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all duration-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-primary-400 dark:focus:ring-primary-400/20"
                                                placeholder="Digite seu comentário..."
                                                required
                                            ></textarea>
                                            <InputError
                                                class="mt-2"
                                                :message="commentForm.errors.body"
                                            />
                                        </div>
                                        <div class="flex justify-end">
                                            <PrimaryButton
                                                :class="{ 'opacity-25': commentForm.processing }"
                                                :disabled="commentForm.processing"
                                            >
                                                <span v-if="!commentForm.processing">Enviar Comentário</span>
                                                <span v-else class="flex items-center">
                                                    <svg
                                                        class="mr-2 h-4 w-4 animate-spin"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <circle
                                                            class="opacity-25"
                                                            cx="12"
                                                            cy="12"
                                                            r="10"
                                                            stroke="currentColor"
                                                            stroke-width="4"
                                                        ></circle>
                                                        <path
                                                            class="opacity-75"
                                                            fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                        ></path>
                                                    </svg>
                                                    Enviando...
                                                </span>
                                            </PrimaryButton>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Error Alert -->
                        <AiErrorAlert
                            v-if="aiError"
                            :error="aiError"
                            @dismiss="dismissError"
                        />

                        <!-- Ações IA -->
                        <div
                            v-if="can.useAi"
                            class="overflow-hidden rounded-2xl bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 shadow-lg border border-primary-200 dark:border-primary-800"
                        >
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <svg
                                        class="w-5 h-5 mr-2 text-primary-600 dark:text-primary-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                                        />
                                    </svg>
                                    Ações IA
                                </h3>
                                <div class="space-y-2">
                                    <button
                                        @click="runAi('summarize')"
                                        class="w-full flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-primary-300 dark:border-primary-700 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 shadow-sm hover:bg-primary-50 dark:hover:bg-primary-900/30 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        Gerar Resumo
                                    </button>
                                    <button
                                        @click="runAi('classify')"
                                        class="w-full flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-primary-300 dark:border-primary-700 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 shadow-sm hover:bg-primary-50 dark:hover:bg-primary-900/30 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                            />
                                        </svg>
                                        Classificar
                                    </button>
                                    <button
                                        @click="runAi('suggestReply')"
                                        class="w-full flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-primary-300 dark:border-primary-700 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 shadow-sm hover:bg-primary-50 dark:hover:bg-primary-900/30 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200"
                                    >
                                        <svg
                                            class="w-4 h-4 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                                            />
                                        </svg>
                                        Sugerir Resposta
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Histórico IA -->
                        <div
                            v-if="can.useAi && ticket.ai_runs && ticket.ai_runs.length > 0"
                            class="overflow-hidden rounded-2xl bg-white shadow-lg dark:bg-gray-800 border border-gray-100 dark:border-gray-700"
                        >
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                                    Histórico IA
                                </h3>
                                <div class="space-y-4">
                                    <div
                                        v-for="run in ticket.ai_runs"
                                        :key="run.id"
                                        class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600"
                                    >
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="run.status === 'success' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'"
                                            >
                                                {{ run.status === 'success' ? 'Sucesso' : 'Erro' }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ formatDateTime(run.created_at) }}
                                            </span>
                                        </div>
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            {{ run.run_type }}
                                        </div>
                                        <div
                                            v-if="run.response"
                                            class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap mt-2 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600"
                                        >
                                            {{ run.response }}
                                        </div>
                                        <div
                                            v-if="run.error_message"
                                            class="text-sm text-red-600 dark:text-red-400 mt-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800"
                                        >
                                            {{ run.error_message }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
