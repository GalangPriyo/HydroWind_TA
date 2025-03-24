<script setup>
import { Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Safe from "@/Components/Safe.vue";
import Danger from "@/Components/Danger.vue";
import Warning from "@/Components/Warning.vue";

defineOptions({ layout: GuestLayout });

const props = defineProps({
    devices: Array, // Menerima daftar devices dari backend
});
</script>

<template>
    <div class="min-h-screen">
        <div class="relative hero min-h-screen">
            <!-- Background Gambar -->
            <div
                class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('/assets/media/sunny.jpg')"
            ></div>

            <!-- Gradient Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-b from-transparent to-base-100"
            ></div>
            <div
                class="hero-content text-base text-center pt-20 md:pt-10 xl:pt-0"
            >
                <div class="flex flex-col items-center text-center gap-10">
                    <div class="max-w-xl">
                        <h1 class="mb-5 text-6xl font-bold">HydroWind</h1>
                        <p class="mb-5">
                            Sistem pemantauan bencana berbasis IoT yang
                            menampilkan data real-time tentang curah hujan,
                            kecepatan angin, dan ketinggian air sungai untuk
                            mendeteksi potensi bencana banjir dan angin kencang
                            di Desa Gebangan.
                        </p>
                    </div>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 gap-5"
                    >
                        <div class="card glass text-base w-full">
                            <div class="card-body items-center text-center">
                                <h2 class="card-title">Status Curah Hujan</h2>
                                <Safe></Safe>
                                <p>
                                    <span class="font-bold">AMAN</span>, tidak
                                    ada potensi bencana.
                                </p>
                            </div>
                        </div>

                        <div class="card glass text-base w-full">
                            <div class="card-body items-center text-center">
                                <h2 class="card-title">
                                    Status Ketinggian Air Sungai
                                </h2>
                                <Danger></Danger>
                                <p>
                                    <span class="font-bold">BAHAYA</span>,
                                    potensi tinggi terjadi bencana
                                </p>
                            </div>
                        </div>

                        <div class="card glass text-base w-full">
                            <div class="card-body items-center text-center">
                                <h2 class="card-title">
                                    Status Kecepatan Angin
                                </h2>
                                <Warning></Warning>
                                <p>
                                    <span class="font-bold">WASPADA</span>,
                                    potensi rendah terjadi bencana
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
