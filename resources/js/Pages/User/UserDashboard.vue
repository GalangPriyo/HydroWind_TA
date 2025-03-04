<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

defineProps({
    user: Object, // User yang sedang login
});
</script>

<template>
    <Head title="User Dashboard" />
    <div class="p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-xl font-bold">User Dashboard</h1>
        <p class="mt-2">Selamat datang, {{ user.name }}!</p>
    </div>
</template>
