<script setup>
import { Link } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Safe from "@/Components/Safe.vue";
import Danger from "@/Components/Danger.vue";
import Warning from "@/Components/Warning.vue";
import { onMounted, nextTick } from "vue";
import Chart from "chart.js/auto";

defineOptions({ layout: GuestLayout });

const props = defineProps({
    devices: Array, // Menerima daftar perangkat dari backend
});

// Fungsi untuk inisialisasi grafik pada setiap sensor
const initCharts = async () => {
    await nextTick(); // Pastikan DOM telah dirender sebelum inisialisasi Chart.js
    props.devices.forEach((device) => {
        device.sensors.forEach((sensor) => {
            const ctx = document
                .getElementById(`chart-${device.id}-${sensor.name}`)
                ?.getContext("2d");
            if (!ctx) return;

            const myChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: Array(10).fill(""),
                    datasets: [
                        {
                            label: sensorLabel(sensor.name),
                            data: sensor.data || Array(10).fill(0), // Data awal
                            borderColor: "rgb(75, 192, 192)",
                            borderWidth: 2,
                            fill: {
                                target: "origin",
                                above: "rgba(75, 192, 192, 0.2)",
                            },
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { display: false },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: "Nilai Sensor",
                                font: { size: 14 },
                            },
                        },
                    },
                },
            });

            // Update data setiap detik
            setInterval(() => {
                const newData = Math.floor(Math.random() * 100);
                myChart.data.datasets[0].data.shift();
                myChart.data.datasets[0].data.push(newData);
                myChart.update();
            }, 1000);
        });
    });
};

onMounted(() => {
    initCharts();
});

// Mapping sensor name ke label yang lebih user-friendly
const sensorLabel = (name) => {
    const labels = {
        rain_intensity: "Intensitas Hujan",
        water_level: "Ketinggian Air Sungai",
        wind_speed: "Kecepatan Angin",
        wind_direction: "Arah Angin",
    };
    return labels[name] || name;
};

// Mapping sensor name ke gambar yang sesuai
const getSensorImage = (name) => {
    const images = {
        rain_intensity: "/assets/media/hujan2.png",
        water_level: "/assets/media/sungai.png",
        wind_speed: "/assets/media/angin.png",
        wind_direction: "/assets/media/kompas.png",
    };
    return images[name] || "/assets/media/default.png";
};
</script>

<template>
    <div class="min-h-screen">
        <div class="mx-6 pb-2">
            <h1 class="text-base text-center font-bold text-3xl pb-6">
                Real-Time Monitoring
            </h1>
            <div>
                <div
                    v-if="devices && devices.length > 0"
                    role="tablist"
                    class="tabs tabs-lifted"
                >
                    <!-- Loop untuk setiap device (titik pantau) -->
                    <template
                        v-for="(device, index) in devices"
                        :key="device.id"
                    >
                        <input
                            type="radio"
                            :id="'tab-' + index"
                            name="device_tabs"
                            role="tab"
                            class="tab"
                            :aria-label="device.name"
                            :checked="index === 0"
                        />
                        <div
                            role="tabpanel"
                            class="tab-content bg-base-100 border-base-300 rounded-tr-lg rounded-br-lg rounded-bl-lg p-6"
                        >
                            <div
                                class="flex flex-wrap gap-[20px] justify-center items-start"
                            >
                                <!-- Loop untuk setiap sensor dalam node -->
                                <div
                                    v-for="sensor in device.sensors"
                                    :key="sensor.name"
                                    class="grow-0"
                                >
                                    <div
                                        class="flex justify-center items-center h-full flex-col"
                                    >
                                        <h1 class="font-bold text-xl pb-2">
                                            {{ sensorLabel(sensor.name) }}
                                        </h1>
                                        <div
                                            class="stats stats-vertical lg:stats-horizontal shadow"
                                        >
                                            <div
                                                class="stat place-items-center"
                                            >
                                                <img
                                                    :src="
                                                        getSensorImage(
                                                            sensor.name
                                                        )
                                                    "
                                                    :alt="
                                                        sensorLabel(sensor.name)
                                                    "
                                                    class="w-24 h-24 object-cover"
                                                />
                                            </div>
                                            <div
                                                class="stat place-items-center w-80"
                                            >
                                                <div
                                                    class="stat-value text-5xl"
                                                >
                                                    {{ sensor.value || 0 }}
                                                </div>
                                                <div
                                                    class="stat-desc text-base"
                                                >
                                                    Diukur dalam satuan,
                                                    <span class="font-bold">{{
                                                        sensor.unit
                                                    }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart untuk setiap sensor -->
                                <div
                                    v-for="sensor in device.sensors"
                                    :key="
                                        'chart-' + device.id + '-' + sensor.name
                                    "
                                    class="grow-[2]"
                                >
                                    <div class="card glass text-base h-80">
                                        <div
                                            class="card-body items-center text-center"
                                        >
                                            <canvas
                                                :id="
                                                    'chart-' +
                                                    device.id +
                                                    '-' +
                                                    sensor.name
                                                "
                                            ></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Tampilkan pesan jika tidak ada data -->
                <p v-else class="text-gray-500 text-center py-4">
                    Tidak ada perangkat terdaftar.
                </p>
            </div>
        </div>
    </div>
</template>
