<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const tickets = computed(() => page.props.tickets);
const filters = computed(() => page.props.filters || {});

const searchQuery = ref(filters.value.q || '');

function search() {
    router.get(route('tickets.index'), { q: searchQuery.value }, {
        preserveState: true,
        replace: true,
    });
}

function getStatusColor(status) {
    const colors = {
        open: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        in_progress: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
        resolved: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        closed: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    };
    return colors[status] || colors.open;
}

function getPriorityColor(priority) {
    const colors = {
        low: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        medium: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        high: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
        urgent: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
    };
    return colors[priority] || colors.medium;
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('pt-PT', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(date);
}
</script>

<style scoped>
.list-item {
    animation: fadeInUp 0.4s ease-out backwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.list-enter-active,
.list-leave-active {
    transition: all 0.3s ease;
}

.list-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.list-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

.list-move {
    transition: transform 0.3s ease;
}
</style>

<template>
    <Head title="Tickets" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100">
                        Tickets
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Gerencie e acompanhe todos os seus tickets de suporte
                    </p>
                </div>
                <Link :href="route('tickets.create')">
                    <PrimaryButton>
                        <svg
                            class="mr-2 h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        Novo Ticket
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <TextInput
                                v-model="searchQuery"
                                @keyup.enter="search"
                                type="text"
                                class="w-full"
                                placeholder="Buscar tickets por título..."
                            />
                        </div>
                        <PrimaryButton @click="search" class="px-6">
                            <svg
                                class="mr-2 h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                            Buscar
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Tickets List -->
                <transition-group
                    v-if="tickets.data && tickets.data.length > 0"
                    name="list"
                    tag="div"
                    class="space-y-4"
                >
                    <Link
                        v-for="(ticket, index) in tickets.data"
                        :key="ticket.id"
                        :href="route('tickets.show', ticket.id)"
                        :style="{ 'animation-delay': `${index * 50}ms` }"
                        class="list-item group block rounded-xl bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-lg hover:-translate-y-1 dark:bg-gray-800 dark:hover:shadow-xl"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <!-- Title and Summary -->
                                <div class="flex items-start gap-3">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-primary-600 dark:text-gray-100 dark:group-hover:text-primary-400 transition-colors">
                                            {{ ticket.title }}
                                        </h3>
                                        <!-- AI Summary -->
                                        <div v-if="ticket.summary" class="mt-2 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800">
                                            <div class="flex items-start gap-2">
                                                <svg
                                                    class="h-4 w-4 text-primary-600 dark:text-primary-400 mt-0.5 flex-shrink-0"
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
                                                <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                                                    {{ ticket.summary }}
                                                </p>
                                            </div>
                                        </div>
                                        <!-- Fallback Description Preview -->
                                        <p v-else-if="ticket.description" class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                            {{ ticket.description }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Meta Information -->
                                <div class="mt-4 flex flex-wrap items-center gap-3">
                                    <span
                                        :class="getStatusColor(ticket.status)"
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium capitalize"
                                    >
                                        {{ ticket.status.replace('_', ' ') }}
                                    </span>
                                    <span
                                        :class="getPriorityColor(ticket.priority)"
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium capitalize"
                                    >
                                        {{ ticket.priority }}
                                    </span>
                                    <span v-if="ticket.category" class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ ticket.category }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        Criado em {{ formatDate(ticket.created_at) }}
                                    </span>
                                    <span v-if="ticket.creator" class="text-xs text-gray-500 dark:text-gray-400">
                                        por {{ ticket.creator.name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Arrow Icon -->
                            <div class="ml-4 flex-shrink-0">
                                <svg
                                    class="h-5 w-5 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </div>
                        </div>
                    </Link>
                </transition-group>

                <!-- Empty State -->
                <div v-else class="rounded-xl bg-white p-12 text-center shadow-sm dark:bg-gray-800">
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400"
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
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Nenhum ticket encontrado
                    </h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ searchQuery ? 'Tente ajustar sua busca.' : 'Comece criando seu primeiro ticket.' }}
                    </p>
                    <div v-if="!searchQuery" class="mt-6">
                        <Link :href="route('tickets.create')">
                            <PrimaryButton>
                                Criar Primeiro Ticket
                            </PrimaryButton>
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="tickets.links && tickets.links.length > 3" class="mt-6 flex items-center justify-center">
                    <div class="flex gap-2">
                        <Link
                            v-for="link in tickets.links"
                            :key="link.url || link.label"
                            :href="link.url || '#'"
                            v-html="link.label"
                            :class="[
                                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                                link.active
                                    ? 'bg-primary-600 text-white'
                                    : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700',
                                !link.url
                                    ? 'opacity-50 cursor-not-allowed pointer-events-none'
                                    : 'cursor-pointer',
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
