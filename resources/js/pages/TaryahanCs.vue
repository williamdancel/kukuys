<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { taryahanCs } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Taryahan CS',
        href: taryahanCs().url,
    },
];
</script>

<template>
    <Head title="Taryahan CS" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl text-center font-semibold">Taryahan CS Page Coming Soon!</h1>
        </div>
    </AppLayout>
</template>
