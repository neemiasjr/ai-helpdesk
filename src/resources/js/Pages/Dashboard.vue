<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2
                        class="text-2xl font-bold leading-tight text-gray-900 dark:text-gray-100"
                    >
                        Dashboard
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Welcome back! Here's what's happening with your tickets.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                    <div
                        class="overflow-hidden rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800 border border-gray-100 dark:border-gray-700"
                    >
                        <div class="flex items-center">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/30"
                            >
                                <svg
                                    class="h-6 w-6 text-primary-600 dark:text-primary-400"
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
                            </div>
                            <div class="ml-4">
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    Total Tickets
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                                >
                                    0
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800 border border-gray-100 dark:border-gray-700"
                    >
                        <div class="flex items-center">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100 dark:bg-yellow-900/30"
                            >
                                <svg
                                    class="h-6 w-6 text-yellow-600 dark:text-yellow-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    Pending
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                                >
                                    0
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800 border border-gray-100 dark:border-gray-700"
                    >
                        <div class="flex items-center">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30"
                            >
                                <svg
                                    class="h-6 w-6 text-blue-600 dark:text-blue-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"
                                    />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    In Progress
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                                >
                                    0
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden rounded-xl bg-white p-6 shadow-lg dark:bg-gray-800 border border-gray-100 dark:border-gray-700"
                    >
                        <div class="flex items-center">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30"
                            >
                                <svg
                                    class="h-6 w-6 text-green-600 dark:text-green-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p
                                    class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                >
                                    Resolved
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                                >
                                    0
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Welcome Card -->
                <div
                    class="overflow-hidden rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 p-8 shadow-xl dark:from-primary-700 dark:to-primary-600"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <h3
                                class="text-2xl font-bold text-white"
                            >
                                Welcome to AI Helpdesk
                            </h3>
                            <p class="mt-2 text-primary-100">
                                Your intelligent support system powered by AI. Manage tickets,
                                get AI-powered suggestions, and deliver exceptional customer
                                service.
                            </p>
                        </div>
                        <div class="hidden md:block">
                            <div
                                class="flex h-24 w-24 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm"
                            >
                                <svg
                                    class="h-12 w-12 text-white"
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
