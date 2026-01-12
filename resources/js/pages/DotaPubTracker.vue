<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dotaPubTracker } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dota Pub Tracker',
        href: dotaPubTracker().url,
    },
];
</script>

<template>
    <Head title="Dota Pub Tracker" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl text-center font-semibold">Dota Pub Tracker Page Coming Soon!</h1>
        </div>
    </AppLayout>
</template>
