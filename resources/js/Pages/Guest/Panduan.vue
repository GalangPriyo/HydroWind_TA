<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

defineOptions({ layout: GuestLayout });

defineProps({
    devices: Array,
});

// Data: infoSlides & currentInfoSlide
const currentInfoSlide = ref(0);
const infoSlides = ref([
    {
        title: "Apa Itu HydroWind?",
    },
    {
        title: "Grup WhatsApp HydroWind",
    },
]);

// Methods
function nextInfoSlide() {
    if (currentInfoSlide.value < infoSlides.value.length - 1) {
        currentInfoSlide.value++;
    }
}

function prevInfoSlide() {
    if (currentInfoSlide.value > 0) {
        currentInfoSlide.value--;
    }
}

// Status formatter
const statusLabel = (status) => {
    switch (status) {
        case "active":
            return "Aktif";
        case "maintenance":
            return "Maintenance";
        case "inactive":
            return "Tidak Aktif";
        default:
            return status;
    }
};
</script>

<template>
    <Head title="Panduan" />
    <div
        class="min-h-screen bg-gradient-to-b from-blue-50 via-cyan-200 to-blue-400"
    >
        <!-- Hero Section -->
        <div class="pt-20 pb-12 px-4 sm:px-6">
            <div class="max-w-7xl mx-auto text-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                    Panduan Sistem Monitoring HydroWind
                </h1>

                <p class="text-base sm:text-lg text-gray-600 max-w-3xl mx-auto">
                    Sistem pemantauan bencana berbasis IoT untuk deteksi dini
                    bencana banjir dan angin kencang
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 pb-20">
            <!-- Introduction Card -->
            <div
                class="bg-white rounded-2xl shadow-lg mb-12 border border-gray-100 overflow-hidden"
            >
                <!-- Slider Controls -->
                <div
                    class="flex justify-between items-center border-b border-gray-200 p-3 bg-gray-50"
                >
                    <div class="flex gap-4 items-center">
                        <button
                            @click="prevInfoSlide"
                            class="bg-gray-100 p-2 rounded-xl hover:bg-gray-200 transition-colors"
                            :disabled="currentInfoSlide === 0"
                            :class="{
                                'opacity-50 cursor-not-allowed':
                                    currentInfoSlide === 0,
                            }"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                        </button>

                        <div class="text-left">
                            <h1
                                class="text-lg sm:text-xl font-bold text-gray-900"
                            >
                                {{ infoSlides[currentInfoSlide].title }}
                            </h1>
                        </div>
                    </div>

                    <button
                        @click="nextInfoSlide"
                        class="bg-gray-100 p-2 rounded-xl hover:bg-gray-200 transition-colors"
                        :disabled="currentInfoSlide === infoSlides.length - 1"
                        :class="{
                            'opacity-50 cursor-not-allowed':
                                currentInfoSlide === infoSlides.length - 1,
                        }"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Slider Content -->
                <div class="relative overflow-hidden">
                    <div
                        class="flex transition-transform duration-300"
                        :style="{
                            transform: `translateX(-${
                                currentInfoSlide * 100
                            }%)`,
                        }"
                    >
                        <!-- Slide 1: About HydroWind -->
                        <div class="w-full flex-shrink-0 py-6 px-4 sm:px-8">
                            <div
                                class="flex flex-col lg:flex-row items-start gap-6"
                            >
                                <div class="flex-shrink-0 order-1 lg:order-1">
                                    <div
                                        class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-6 h-6 text-blue-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>
                                </div>

                                <div class="flex-1 order-3 lg:order-2">
                                    <p class="text-gray-700 leading-relaxed">
                                        <strong class="text-blue-600"
                                            >HydroWind</strong
                                        >
                                        adalah sistem pemantauan bencana
                                        berbasis Internet of Things (IoT) yang
                                        menggunakan sensor untuk mendeteksi
                                        potensi
                                        <strong class="text-blue-600"
                                            >Banjir</strong
                                        >
                                        dan
                                        <strong class="text-blue-600"
                                            >Angin Kencang</strong
                                        >
                                        secara real-time.
                                    </p>

                                    <p
                                        class="text-gray-700 leading-relaxed mt-3"
                                    >
                                        Selain tampilan data real-time,
                                        <strong class="text-blue-600"
                                            >HydroWind</strong
                                        >
                                        juga dilengkapi dengan sistem peringatan
                                        dini yang akan mengirim notifikasi
                                        melalui
                                        <strong class="text-blue-600"
                                            >WhatsApp</strong
                                        >
                                        jika terdeteksi kondisi berbahaya,
                                        membantu masyarakat dan pihak berwenang
                                        untuk merespons dengan cepat.
                                    </p>

                                    <p
                                        class="text-gray-700 leading-relaxed mt-3"
                                    >
                                        Cakupan wilayah pemantauan berada di
                                        <strong class="text-blue-600"
                                            >Desa Gebangan, Kabupaten
                                            Grobogan</strong
                                        >, yang merupakan salah satu daerah
                                        dengan risiko bencana banjir dan angin
                                        kencang.
                                    </p>
                                </div>

                                <div
                                    class="flex-shrink-0 order-2 lg:order-3 mx-auto lg:mx-0"
                                >
                                    <img
                                        src="/assets/media/info.png"
                                        alt="Ilustrasi HydroWind"
                                        class="w-48 sm:w-64 lg:w-72 h-auto rounded-xl object-cover"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2: Coverage Area -->
                        <div class="w-full flex-shrink-0 py-6 px-4 sm:px-8">
                            <div
                                class="flex flex-col lg:flex-row items-start gap-6"
                            >
                                <div class="flex-shrink-0 order-1 lg:order-1">
                                    <div
                                        class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center"
                                    >
                                        <i
                                            class="fa-brands fa-whatsapp text-blue-600 text-2xl"
                                        ></i>
                                    </div>
                                </div>

                                <div class="flex-1 order-3 lg:order-2">
                                    <p class="text-gray-700 leading-relaxed">
                                        <strong class="text-blue-600"
                                            >HydroWind</strong
                                        >
                                        juga dilengkapi dengan sistem peringatan
                                        dini yang akan mengirim notifikasi
                                        melalui
                                        <strong class="text-blue-600"
                                            >WhatsApp</strong
                                        >
                                        jika terdeteksi kondisi berbahaya,
                                        membantu masyarakat dan pihak berwenang
                                        untuk merespons dengan cepat.
                                    </p>
                                    <p
                                        class="text-gray-700 leading-relaxed mt-3"
                                    >
                                        Jika Anda ingin mendapatkan notifikasi
                                        peringatan dini terkait potensi bencana
                                        banjir dan angin kencang secara
                                        real-time, Anda dapat bergabung dengan
                                        <strong class="text-blue-600"
                                            >Grup WhatsApp Komunitas
                                            HydroWind</strong
                                        >. Klik tombol di bawah ini untuk
                                        bergabung ke grup dan mendapatkan
                                        notifikasi peringatan apabila terjadi
                                        bencana.
                                    </p>
                                    <div
                                        class="flex justify-center md:justify-start mt-6"
                                    >
                                        <a
                                            href="https://chat.whatsapp.com/I2N74ilIQdbJtZCsnk1HeW"
                                            target="_blank"
                                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg text-sm hover:bg-blue-700 transition w-max"
                                        >
                                            <i
                                                class="fa-solid fa-link mr-1"
                                            ></i>
                                            Grup WhatsApp Hydrowind
                                        </a>
                                    </div>
                                </div>

                                <div
                                    class="flex-shrink-0 order-2 lg:order-3 mx-auto lg:mx-0"
                                >
                                    <img
                                        src="/assets/media/grup_panduan.png"
                                        alt="Ilustrasi HydroWind"
                                        class="w-auto h-48 sm:h-56 lg:h-64 rounded-xl object-cover"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide Indicators -->
                <div
                    class="flex justify-center space-x-2 p-4 border-t border-gray-100"
                >
                    <button
                        v-for="(slide, index) in infoSlides"
                        :key="index"
                        @click="currentInfoSlide = index"
                        class="w-2 h-2 rounded-full transition-colors"
                        :class="{
                            'bg-blue-500': currentInfoSlide === index,
                            'bg-gray-300': currentInfoSlide !== index,
                        }"
                    ></button>
                </div>
            </div>

            <!-- Devices Grid -->
            <div class="mb-12">
                <div class="flex items-center space-x-3 mb-6">
                    <div
                        class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"
                    >
                        <i
                            class="fa-solid fa-satellite-dish text-blue-600 text-lg"
                        ></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        Informasi Node Monitoring
                    </h2>
                </div>

                <!-- Tambahkan kondisi jika tidak ada devices -->
                <div
                    v-if="devices.length === 0"
                    class="bg-white rounded-xl shadow-md p-6 text-center border border-gray-100"
                >
                    <i
                        class="fas fa-exclamation-circle text-blue-600 text-6xl mb-3"
                    ></i>
                    <h3 class="text-xl font-medium text-gray-700 mb-2">
                        Tidak Ada Perangkat Terdaftar
                    </h3>
                    <p class="text-gray-500">
                        Saat ini belum ada perangkat monitoring yang terdaftar
                        dalam sistem.
                    </p>
                </div>

                <div
                    v-else
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="device in devices"
                        :key="device.id"
                        class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 px-6 py-3 border border-gray-100"
                    >
                        <!-- Header dengan gradient -->
                        <div
                            class="bg-primary px-6 py-3 -mx-6 -my-3 mb-4 rounded-t-xl"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-white font-bold text-lg">
                                        {{ device.name }}
                                    </h3>
                                    <p class="text-blue-100 text-sm">
                                        ID: {{ device.node_id }}
                                    </p>
                                </div>
                                <span
                                    :class="[
                                        'px-3 py-1 rounded-full text-xs font-medium',
                                        {
                                            'bg-transparent text-emerald-400 border border-emerald-400':
                                                device.status === 'active',
                                            'bg-transparent text-amber-400 border border-amber-400':
                                                device.status === 'maintenance',
                                            'bg-transparent text-red-500 border border-red-500':
                                                device.status === 'inactive',
                                        },
                                    ]"
                                >
                                    {{ statusLabel(device.status) }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-start space-x-3">
                                <i
                                    class="fa-solid fa-location-dot mt-1 text-gray-500 mt-0.5"
                                ></i>
                                <span class="text-sm text-gray-600">{{
                                    device.location
                                }}</span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <i
                                    class="fa-solid fa-location-crosshairs text-gray-500"
                                ></i>
                                <span class="text-sm text-gray-600"
                                    >{{ device.latitude }},
                                    {{ device.longitude }}</span
                                >
                            </div>

                            <div class="pt-2 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">
                                    Sensor Terpasang :
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-if="device.sensors.length"
                                        v-for="sensor in device.sensors"
                                        :key="sensor.id"
                                        class="px-2 py-1 bg-blue-50 text-primary text-xs rounded-md"
                                    >
                                        {{ sensor.name.replace("_", " ") }}
                                    </span>

                                    <span v-else class="text-xs text-gray-400"
                                        >Tidak ada sensor</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Table -->
            <div class="mb-12">
                <div class="flex items-center space-x-3 mb-6">
                    <div
                        class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"
                    >
                        <svg
                            class="w-6 h-6 text-blue-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                            ></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        Status Bencana dan Ambang Batas
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <div
                        class="min-w-[800px] md:min-w-full overflow-hidden rounded-xl border border-gray-200 shadow-sm"
                    >
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <!-- Baris pertama header -->
                                <tr>
                                    <th
                                        class="w-[10%] py-4 px-3 text-center text-lg font-medium text-gray-700 border-b border-gray-200"
                                        rowspan="2"
                                    >
                                        <div
                                            class="flex items-center justify-center gap-2"
                                        >
                                            Status
                                        </div>
                                    </th>
                                    <th
                                        class="w-[20%] py-4 px-3 text-center text-lg font-medium text-gray-700 border-b border-gray-200"
                                        rowspan="2"
                                    >
                                        Keterangan
                                    </th>
                                    <th
                                        class="py-4 px-3 text-center text-lg font-medium text-gray-700 border-b border-gray-200"
                                        colspan="4"
                                    >
                                        Ambang Batas
                                    </th>
                                </tr>
                                <!-- Baris kedua header -->
                                <tr class="bg-gray-50">
                                    <th
                                        class="w-[17.5%] py-3 px-3 text-center font-medium text-gray-700 border-b border-gray-200"
                                    >
                                        <div
                                            class="flex items-center gap-2 justify-center"
                                        >
                                            <i
                                                class="fas fa-cloud-showers-heavy text-blue-500"
                                            ></i>
                                            <span class="whitespace-nowrap"
                                                >Curah Hujan</span
                                            >
                                        </div>
                                    </th>
                                    <th
                                        class="w-[17.5%] py-3 px-3 text-center font-medium text-gray-700 border-b border-gray-200"
                                    >
                                        <div
                                            class="flex items-center gap-2 justify-center"
                                        >
                                            <i
                                                class="fas fa-water text-blue-500"
                                            ></i>
                                            <span class="whitespace-nowrap"
                                                >Ketinggian Air</span
                                            >
                                        </div>
                                    </th>
                                    <th
                                        class="w-[17.5%] py-3 px-3 text-center font-medium text-gray-700 border-b border-gray-200"
                                    >
                                        <div
                                            class="flex items-center gap-2 justify-center"
                                        >
                                            <i
                                                class="fas fa-wind text-blue-500"
                                            ></i>
                                            <span class="whitespace-nowrap"
                                                >Kecepatan Angin</span
                                            >
                                        </div>
                                    </th>
                                    <!-- <th
                                        class="w-[17.5%] py-3 px-3 text-center font-medium text-gray-700 border-b border-gray-200"
                                    >
                                        <div
                                            class="flex items-center gap-2 justify-center"
                                        >
                                            <i
                                                class="fas fa-temperature-three-quarters text-blue-500"
                                            ></i>
                                            <span class="whitespace-nowrap"
                                                >Tekanan Udara</span
                                            >
                                        </div>
                                    </th> -->
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 bg-white">
                                <!-- Baris Aman -->
                                <tr
                                    class="hover:bg-green-50 transition-colors group"
                                >
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-2.5 h-2.5 rounded-full bg-green-500 group-hover:bg-green-600"
                                            ></div>
                                            <span
                                                class="font-medium text-green-700 whitespace-nowrap"
                                                >Aman</span
                                            >
                                            <i
                                                class="fas fa-check-circle text-green-400 opacity-0 group-hover:opacity-100 transition-opacity"
                                            ></i>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3 text-gray-600">
                                        Seluruh parameter berada dalam rentang
                                        normal.
                                    </td>
                                    <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-green-100 text-green-800 px-2 py-1 rounded whitespace-nowrap"
                                            >&lt; 100 mm</span
                                        >
                                    </td>
                                    <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-green-100 text-green-800 px-2 py-1 rounded whitespace-nowrap"
                                            >&lt; 150 cm</span
                                        >
                                    </td>
                                    <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-green-100 text-green-800 px-2 py-1 rounded whitespace-nowrap"
                                            >&lt; 38 km/jam</span
                                        >
                                    </td>
                                    <!-- <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-green-100 text-green-800 px-2 py-1 rounded whitespace-nowrap"
                                            >&lt; 800 hPa</span
                                        >
                                    </td> -->
                                </tr>

                                <!-- Baris Waspada -->
                                <tr
                                    class="hover:bg-amber-50 transition-colors group"
                                >
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-2.5 h-2.5 rounded-full bg-amber-500 group-hover:bg-amber-600"
                                            ></div>
                                            <span
                                                class="font-medium text-amber-700 whitespace-nowrap"
                                                >Waspada</span
                                            >
                                            <i
                                                class="fas fa-exclamation-triangle text-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"
                                            ></i>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3 text-gray-600">
                                        Terjadi peningkatan nilai parameter yang
                                        perlu diperhatikan.
                                    </td>
                                    <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-amber-100 text-amber-800 px-2 py-1 rounded whitespace-nowrap"
                                            >≥ 100 mm</span
                                        >
                                    </td>
                                    <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-amber-100 text-amber-800 px-2 py-1 rounded whitespace-nowrap"
                                            >≥ 120 cm</span
                                        >
                                    </td>
                                    <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-amber-100 text-amber-800 px-2 py-1 rounded whitespace-nowrap"
                                            >≥ 38 km/jam</span
                                        >
                                    </td>
                                    <!-- <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-amber-100 text-amber-800 px-2 py-1 rounded whitespace-nowrap"
                                            >≥ 800 hPa</span
                                        >
                                    </td> -->
                                </tr>

                                <!-- Baris Bahaya -->
                                <tr
                                    class="hover:bg-red-50 transition-colors group"
                                >
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-2.5 h-2.5 rounded-full bg-red-500 group-hover:bg-red-600"
                                            ></div>
                                            <span
                                                class="font-medium text-red-700 whitespace-nowrap"
                                                >Bahaya</span
                                            >
                                            <i
                                                class="fas fa-skull-crossbones text-red-400 opacity-0 group-hover:opacity-100 transition-opacity"
                                            ></i>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3 text-gray-600">
                                        Parameter menunjukkan keadaan darurat,
                                        tindakan segera diperlukan.
                                    </td>
                                    <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-red-100 text-red-800 px-2 py-1 rounded whitespace-nowrap"
                                            >≥ 150 mm</span
                                        >
                                    </td>
                                    <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-red-100 text-red-800 px-2 py-1 rounded whitespace-nowrap"
                                            >≥ 150 cm</span
                                        >
                                    </td>
                                    <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-red-100 text-red-800 px-2 py-1 rounded whitespace-nowrap"
                                            >≥ 50 km/jam</span
                                        >
                                    </td>
                                    <!-- <td
                                        class="py-4 px-6 text-gray-600 font-mono text-sm text-center"
                                    >
                                        <span
                                            class="bg-red-100 text-red-800 px-2 py-1 rounded whitespace-nowrap"
                                            >≥ 1400 hPa</span
                                        >
                                    </td> -->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Emergency Guidelines -->
            <div class="mb-12">
                <div class="flex items-center space-x-3 mb-6">
                    <div
                        class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"
                    >
                        <i
                            class="fa-regular fa-lightbulb text-blue-600 text-xl"
                        ></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        Panduan Tanggap Darurat
                    </h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Flood Guidelines -->
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden"
                    >
                        <!-- Header dengan gradient -->
                        <div class="bg-primary px-6 py-4 -mx-0 -mt-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center"
                                    >
                                        <i
                                            class="fa-solid fa-house-flood-water text-white"
                                        ></i>
                                    </div>
                                    <h3 class="text-white font-bold text-lg">
                                        Banjir
                                    </h3>
                                </div>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium bg-transparent text-blue-100 border border-blue-100"
                                >
                                    Rekomendasi
                                </span>
                            </div>
                        </div>

                        <div class="px-6 py-4 space-y-4">
                            <ul class="space-y-3">
                                <li class="flex items-start space-x-3">
                                    <i
                                        class="fas fa-exclamation-triangle text-blue-500 mt-0.5 text-sm"
                                    ></i>
                                    <span class="text-gray-700">
                                        Hindari daerah dataran rendah dan dekat
                                        sungai.
                                    </span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <i
                                        class="fas fa-file-alt text-blue-500 mt-0.5 text-sm"
                                    ></i>
                                    <span class="text-gray-700">
                                        Simpan dokumen penting di tempat tinggi.
                                    </span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <i
                                        class="fas fa-bolt text-blue-500 mt-0.5 text-sm"
                                    ></i>
                                    <span class="text-gray-700">
                                        Matikan listrik jika air mulai masuk.
                                    </span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <i
                                        class="fas fa-phone-alt text-blue-500 mt-0.5 text-sm"
                                    ></i>
                                    <span class="text-gray-700">
                                        Simpan nomor darurat dan hubungi jika
                                        diperlukan.
                                    </span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <i
                                        class="fa-solid fa-suitcase-rolling text-blue-500 mt-0.5 text-sm"
                                    ></i>
                                    <span class="text-gray-700">
                                        Siapkan tas siaga berisi kebutuhan
                                        penting (air, makanan, P3K).
                                    </span>
                                </li>
                            </ul>

                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">
                                    Pengingat:
                                </p>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"
                                    ></div>
                                    <span class="text-sm text-gray-700"
                                        >Pantau terus perkembangan melalui
                                        website HydroWind</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wind Guidelines -->
                    <div
                        class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden"
                    >
                        <!-- Header dengan gradient orange -->
                        <div class="bg-primary px-6 py-4 -mx-0 -mt-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center"
                                    >
                                        <i
                                            class="fa-solid fa-tornado text-white"
                                        ></i>
                                    </div>
                                    <h3 class="text-white font-bold text-lg">
                                        Angin Kencang
                                    </h3>
                                </div>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium bg-transparent text-blue-100 border border-blue-100"
                                >
                                    Rekomendasi
                                </span>
                            </div>
                        </div>

                        <div class="px-6 py-4 space-y-4">
                            <ul class="space-y-3">
                                <li class="flex items-start space-x-3">
                                    <i
                                        class="fas fa-tree text-blue-500 mt-0.5 text-sm"
                                    ></i>
                                    <span class="text-gray-700">
                                        Hindari berada di luar ruangan atau
                                        dekat pohon besar.
                                    </span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <i
                                        class="fas fa-home text-blue-500 mt-0.5 text-sm"
                                    ></i>
                                    <span class="text-gray-700">
                                        Perkuat atap rumah jika perlu.
                                    </span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <i
                                        class="fas fa-sign text-blue-500 mt-0.5 text-sm"
                                    ></i>
                                    <span class="text-gray-700">
                                        Jangan berlindung di bawah baliho atau
                                        papan iklan.
                                    </span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <i
                                        class="fas fa-door-closed text-blue-500 mt-0.5 text-sm"
                                    ></i>
                                    <span class="text-gray-700">
                                        Tutup semua jendela dan pintu untuk
                                        mencegah angin masuk.
                                    </span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <i
                                        class="fas fa-car-crash text-blue-500 mt-0.5 text-sm"
                                    ></i>
                                    <span class="text-gray-700">
                                        Hindari berkendara saat angin kencang,
                                        terutama di jalan terbuka.
                                    </span>
                                </li>
                            </ul>

                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">
                                    Pengingat:
                                </p>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"
                                    ></div>
                                    <span class="text-sm text-gray-700"
                                        >Pantau terus perkembangan melalui
                                        website HydroWind</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
