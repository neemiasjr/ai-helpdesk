<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gradient-to-br from-primary-50 via-white to-primary-50 pt-6 sm:justify-center sm:pt-0 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900"
    >
        <div class="mb-4">
            <Link href="/" class="flex flex-col items-center group">
                <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden">
                    <ApplicationLogo class="h-full w-full transition-transform duration-300 group-hover:scale-110" />
                </div>
                <span class="mt-3 text-2xl font-bold text-gray-800 dark:text-gray-100">
                    AI Helpdesk
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Intelligent Support System
                </span>
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white/80 backdrop-blur-sm px-6 py-8 shadow-xl sm:max-w-md sm:rounded-2xl dark:bg-gray-800/80 border border-gray-200/50 dark:border-gray-700/50"
        >
            <slot />
        </div>
    </div>
</template>
