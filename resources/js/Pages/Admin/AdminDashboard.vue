<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineOptions({ layout: AuthenticatedLayout });

defineProps({
    user: Object, // User yang sedang login
});
</script>

<template>
    <Head title="Admin Dashboard" />
    <div class="p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-xl font-bold">Admin Dashboard</h1>
        <p class="mt-2">Selamat datang, Admin {{ user.name }}!</p>
    </div>
    <div class="card bg-base-100 w-96 shadow-xl">
        <figure>
            <img
                src="https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp"
                alt="Shoes"
            />
        </figure>
        <div class="card-body">
            <h2 class="card-title">Shoes!</h2>
            <p>If a dog chews shoes whose shoes does he choose?</p>
            <div class="card-actions justify-end">
                <button class="btn btn-primary">Buy Now</button>
            </div>
        </div>
    </div>
    <div class="p-6 bg-base-100 text-base-content">
        <h1 class="text-2xl font-bold">Halo, Admin!</h1>
        <p>Selamat datang di dashboard.</p>
    </div>
</template>
