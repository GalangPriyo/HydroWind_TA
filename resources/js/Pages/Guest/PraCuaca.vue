<script setup>
import { Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

defineOptions({ layout: GuestLayout });
</script>

<template>
    <div
        class="flex flex-col items-center justify-center min-h-screen space-y-4 bg-blue-200"
    >
        <h1 class="text-2xl font-bold">Halaman Prakiraan Cuaca</h1>
        <p>Ini adalah halaman utama aplikasi HydroWind.</p>
        <div class="flex space-x-4">
            <Link
                href="/login"
                class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600"
                >Login</Link
            >
            <Link
                href="/register"
                class="px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600"
                >Register</Link
            >
        </div>
    </div>
</template>
