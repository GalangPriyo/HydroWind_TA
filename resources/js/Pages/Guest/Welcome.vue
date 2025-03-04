<script setup>
import { Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Safe from "@/Components/Safe.vue";
import Danger from "@/Components/Danger.vue";
import Warning from "@/Components/Warning.vue";

defineOptions({ layout: GuestLayout });
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
            <div class="hero-content text-base text-center">
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
                    <div class="grid grid-cols-3 gap-5">
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
        <!-- <div class="card lg:card-side bg-base-100 shadow-xl mx-4 mt-4">
            <figure>
                <img
                    src="https://img.daisyui.com/images/stock/photo-1494232410401-ad00d5433cfa.webp"
                    alt="Album"
                />
            </figure>
            <div class="card-body">
                <h2 class="card-title">New album is released!</h2>
                <p>Click the button to listen on Spotiwhy app.</p>
                <div class="card-actions justify-end">
                    <button class="btn btn-primary">Listen</button>
                </div>
            </div>
        </div> -->
        <div class="mx-6 pb-2">
            <h1 class="text-base text-center font-bold text-3xl pb-6">
                Real-Time Monitoring
            </h1>
            <div role="tablist" class="tabs tabs-lifted">
                <input
                    type="radio"
                    name="my_tabs_2"
                    role="tab"
                    class="tab"
                    aria-label="Titik Pantau 1"
                    checked="checked"
                />
                <div
                    role="tabpanel"
                    class="tab-content bg-base-100 border-base-300 rounded-tr-lg rounded-br-lg rounded-bl-lg p-6"
                >
                    <div class="grid grid-cols-5 grid-rows-6 gap-4">
                        <div class="col-span-2 row-span-2">
                            <div
                                class="flex justify-center items-center h-full flex-col"
                            >
                                <h1 class="font-bold text-xl pb-2">
                                    Intensitas Hujan
                                </h1>
                                <div
                                    class="stats stats-vertical lg:stats-horizontal shadow"
                                >
                                    <div class="stat place-items-center">
                                        <img
                                            src="/assets/media/hujan2.png"
                                            alt="Sungai"
                                            class="w-24 h-24 object-cover"
                                        />
                                    </div>
                                    <div class="stat place-items-center w-80">
                                        <div class="stat-value text-5xl">
                                            31
                                        </div>
                                        <div class="stat-desc text-base">
                                            Diukur dalam satuan,
                                            <span class="font-bold"
                                                >mm/jam</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-3 row-span-2 col-start-3">
                            <div class="card glass text-base h-full">
                                <div
                                    class="card-body items-center text-center"
                                ></div>
                            </div>
                        </div>
                        <div class="col-span-2 row-span-2 row-start-3">
                            <div
                                class="flex justify-center items-center h-full flex-col"
                            >
                                <h1 class="font-bold text-xl pb-2">
                                    Ketinggian Air Sungai
                                </h1>
                                <div
                                    class="stats stats-vertical lg:stats-horizontal shadow"
                                >
                                    <div class="stat place-items-center">
                                        <img
                                            src="/assets/media/sungai.png"
                                            alt="Sungai"
                                            class="w-24 h-24 object-cover"
                                        />
                                    </div>
                                    <div class="stat place-items-center w-80">
                                        <div class="stat-value text-5xl">
                                            31
                                        </div>
                                        <div class="stat-desc text-base">
                                            Diukur dalam satuan,
                                            <span class="font-bold">cm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-span-3 row-span-2 col-start-3 row-start-3"
                        >
                            <div class="card glass text-base h-full">
                                <div
                                    class="card-body items-center text-center"
                                ></div>
                            </div>
                        </div>
                        <div class="col-span-2 row-span-2 row-start-5">
                            <div
                                class="flex justify-center items-center h-full flex-col"
                            >
                                <h1 class="font-bold text-xl pb-2">
                                    Kecepatan dan Arah Angin
                                </h1>
                                <div
                                    class="stats stats-vertical lg:stats-horizontal shadow"
                                >
                                    <div class="stat place-items-center">
                                        <img
                                            src="/assets/media/angin.png"
                                            alt="Sungai"
                                            class="w-24 h-24 object-cover"
                                        />
                                    </div>
                                    <div class="stat place-items-center w-80">
                                        <div class="stat-value text-5xl">
                                            31
                                        </div>
                                        <div class="stat-desc text-base">
                                            Diukur dalam satuan,
                                            <span class="font-bold">m/s</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-span-3 row-span-2 col-start-3 row-start-5"
                        >
                            <div class="card glass text-base h-full">
                                <div
                                    class="card-body items-center text-center"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <input
                    type="radio"
                    name="my_tabs_2"
                    role="tab"
                    class="tab"
                    aria-label="Titik Pantau 2"
                />
                <div
                    role="tabpanel"
                    class="tab-content bg-base-100 border-base-300 rounded-tr-lg rounded-br-lg rounded-bl-lg p-6"
                >
                    Titik Pantau 2
                </div>

                <input
                    type="radio"
                    name="my_tabs_2"
                    role="tab"
                    class="tab"
                    aria-label="Titik Pantau 3"
                />
                <div
                    role="tabpanel"
                    class="tab-content bg-base-100 border-base-300 rounded-tr-lg rounded-br-lg rounded-bl-lg p-6"
                >
                    Titik Pantau 3
                </div>
            </div>
        </div>
    </div>
</template>
