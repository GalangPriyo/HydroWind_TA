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
    devices: Array, // Menerima daftar devices dari backend
});

const getRandomColor = () => {
    const r = Math.floor(Math.random() * 256);
    const g = Math.floor(Math.random() * 256);
    const b = Math.floor(Math.random() * 256);
    return `rgb(${r}, ${g}, ${b})`;
};

const charts = []; // Deklarasi array global untuk menyimpan referensi Chart.js

const initCharts = async () => {
    await nextTick();
    props.devices.forEach((device, deviceIndex) => {
        device.sensors.forEach((sensor, sensorIndex) => {
            const chartId = `chart-${deviceIndex}-${sensorIndex}`;
            const ctx = document.getElementById(chartId)?.getContext("2d");
            if (!ctx) return;

            // Hapus chart lama jika sudah ada
            const existingChart = charts.find((c) => c.id === chartId);
            if (existingChart) {
                existingChart.chart.destroy();
            }

            const color = getRandomColor();
            const myChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: Array(10).fill(""),
                    datasets: [
                        {
                            label: sensorLabel(sensor.name),
                            data: sensor.data || Array(10).fill(0),
                            borderColor: color, // Warna garis acak
                            backgroundColor: color
                                .replace("rgb", "rgba")
                                .replace(")", ", 0.2)"),
                            borderWidth: 2,
                            fill: {
                                target: "origin",
                                above: color
                                    .replace("rgb", "rgba")
                                    .replace(")", ", 0.2)"), // Warna area transparan
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

            // Simpan referensi chart
            charts.push({ id: chartId, chart: myChart });

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

onMounted(async () => {
    if (props.devices && props.devices.length > 0) {
        initCharts();
    }
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
    <div class="mt-20 w-[90%] mx-auto pb-10">
        <p class="text-center font-bold text-2xl sm:text-3xl pb-6">
            Real-Time Monitoring
        </p>
        <div>
            <div v-if="devices && devices.length > 0" class="tabs tabs-box">
                <!-- Loop untuk setiap device (titik pantau) -->
                <template v-for="(device, index) in devices" :key="device.id">
                    <input
                        type="radio"
                        :id="'tab-' + index"
                        name="device_tabs"
                        class="tab"
                        :aria-label="device.name"
                        :checked="index === 0"
                    />
                    <div class="tab-content bg-base-100 border-base-300 p-6">
                        <div
                            v-for="(sensor, sensorIndex) in device.sensors"
                            :key="sensor.name"
                            class="flex flex-wrap gap-[20px] justify-center items-start py-4"
                        >
                            <!-- Loop untuk setiap sensor dalam node -->
                            <div class="grow-0">
                                <div
                                    class="flex justify-center items-center h-full flex-col"
                                >
                                    <h1 class="font-bold text-xl pb-2">
                                        {{ sensorLabel(sensor.name) }}
                                    </h1>
                                    <div class="stats stats-vertical shadow">
                                        <div class="stat place-items-center">
                                            <img
                                                :src="
                                                    getSensorImage(sensor.name)
                                                "
                                                :alt="sensorLabel(sensor.name)"
                                                class="w-24 h-24 object-cover"
                                            />
                                        </div>
                                        <div
                                            class="stat place-items-center w-80"
                                        >
                                            <div class="stat-value text-5xl">
                                                {{ sensor.value || 0 }}
                                            </div>
                                            <div class="stat-desc text-base">
                                                Diukur dalam satuan,
                                                <span class="font-bold">{{
                                                    sensor.unit
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Grafik Sensor -->
                            <div class="grow-[2]">
                                <div class="card glass text-base h-80">
                                    <div
                                        class="card-body items-center text-center"
                                    >
                                        <canvas
                                            :id="
                                                'chart-' +
                                                index +
                                                '-' +
                                                sensorIndex
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
</template>
