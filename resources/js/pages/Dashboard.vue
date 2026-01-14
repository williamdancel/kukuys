<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl text-center font-semibold text-gray-500">Welcome to the Kukuys Admin Dashboard!</h1>
            <p class="text-sm text-center text-red-500"><span class="font-semibold">Friendly Reminder:</span><br> 
                If you accidentally leaked your account on stream.<br>
                Just change your password by clicking the bottom left corner where your name is displayed<br>
                then clicked settings then click password.<br>
                Additonally please <span class="font-semibold">do not use your personal passwords</span> that you use on other platforms<br>
                just use a <span class="font-semibold">unique password only</span> for Kukuys platform.<br>
                Thank you.

            </p>
        </div>
    </AppLayout>
</template>
