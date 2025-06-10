<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { onMounted, nextTick, ref, computed, onUnmounted } from "vue";
import Chart from "chart.js/auto";
import Echo from "laravel-echo";
import { Head } from "@inertiajs/vue3";

defineOptions({ layout: GuestLayout });

const props = defineProps({
    registeredNodeIds: Array,
    initialDevicesData: Array,
});

const validNodeIds = ref(props.registeredNodeIds || []);
const mqttData = ref(props.initialDevicesData || []); // Gabungan data awal dan realtime
const charts = ref([]);
const activeNode = ref(null); // Node yang sedang aktif menerima data realtime
const isUpdatingChart = ref(false);

const sensorLabel = (name) => {
    const labels = {
        kecepatan_angin: "Kecepatan Angin",
        ketinggian_air: "Ketinggian Air Sungai",
        curah_hujan: "Curah Hujan",
        tekanan_udara: "Tekanan Udara",
    };
    return labels[name] || "Sensor Tidak Dikenal";
};

const getSensorIcon = (name) => {
    const icons = {
        kecepatan_angin: "fas fa-wind", // ikon angin
        ketinggian_air: "fas fa-water", // ikon air
        curah_hujan: "fas fa-cloud-showers-heavy", // ikon hujan
        tekanan_udara: "fas fa-tachometer-alt", // ikon tekanan
    };
    return icons[name] || "fas fa-circle-xmark"; // ikon default
};

const getRandomColor = () => {
    const r = Math.floor(Math.random() * 256);
    const g = Math.floor(Math.random() * 256);
    const b = Math.floor(Math.random() * 256);
    return `rgb(${r}, ${g}, ${b})`;
};

// Status berdasarkan kondisi
const monitoringStatus = computed(() => {
    if (validNodeIds.value.length === 0) {
        return "no_nodes_registered";
    } else if (mqttData.value.length > 0 && activeNode.value === null) {
        return "has_nodes_but_no_realtime";
    } else if (mqttData.value.length > 0 && activeNode.value !== null) {
        return "has_nodes_and_realtime";
    }
    return "no_data";
});

// Modifikasi fungsi updateOrCreateChart
const updateOrCreateChart = async (deviceIndex, sensorIndex, value) => {
    if (isUpdatingChart.value) return;
    isUpdatingChart.value = true;

    await nextTick();

    const chartId = `chart-${deviceIndex}-${sensorIndex}`;
    const canvas = document.getElementById(chartId);

    if (!canvas) {
        console.warn(`Canvas element not found: ${chartId}`);
        isUpdatingChart.value = false;
        return;
    }

    // Periksa apakah canvas sudah memiliki instance chart sebelumnya
    if (canvas.chart) {
        try {
            canvas.chart.destroy();
        } catch (e) {
            console.error("Error destroying existing chart:", e);
        }
    }

    const ctx = canvas.getContext("2d");
    if (!ctx) {
        isUpdatingChart.value = false;
        return;
    }

    try {
        const device = mqttData.value[deviceIndex];
        const sensor = device.sensors[sensorIndex];

        // Buat chart baru setiap kali (lebih stabil)
        const newChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: Array.from({ length: 10 }, (_, i) => ""),
                datasets: [
                    {
                        label: sensorLabel(sensor.type),
                        data: sensor.data || Array(10).fill(sensor.value),
                        borderColor: getRandomColor(),
                        borderWidth: 2,
                        tension: 0.1,
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 0,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                    },
                },
            },
        });

        // Simpan referensi chart langsung di canvas
        canvas.chart = newChart;

        // Update array charts
        const existingChartIndex = charts.value.findIndex(
            (c) => c.id === chartId
        );
        if (existingChartIndex !== -1) {
            charts.value[existingChartIndex].chart = newChart;
        } else {
            charts.value.push({ id: chartId, chart: newChart });
        }

        console.log(`Chart created/updated: ${chartId}`);
    } catch (error) {
        console.error("Error in updateOrCreateChart:", error);
    } finally {
        isUpdatingChart.value = false;
    }
};

const extractNumericValue = (value) => {
    if (typeof value === "number") return value;
    if (typeof value !== "string") return 0;

    // Ekstrak angka dari string (contoh: "4m/s" -> 4)
    const numericMatch = value.match(/[\d.]+/);
    return numericMatch ? parseFloat(numericMatch[0]) : 0;
};

// Callback saat data dari MQTT masuk
// Callback saat data dari MQTT masuk
function handleMQTTData(newData) {
    if (
        !newData ||
        typeof newData.sensor !== "object" ||
        newData.sensor === null
    ) {
        return;
    }

    if (!validNodeIds.value.includes(newData.node_id)) {
        console.warn("Data dari node_id tidak dikenal:", newData.node_id);
        return;
    }

    activeNode.value = newData.node_id;

    // Definisikan urutan sensor yang diinginkan
    const sensorOrder = [
        "curah_hujan",
        "kecepatan_angin",
        "ketinggian_air",
        "tekanan_udara",
    ];

    const sensorUnits = {
        kecepatan_angin: "m/s",
        ketinggian_air: "cm",
        curah_hujan: "mm",
        tekanan_udara: "mb",
    };

    // Format sensor data sesuai urutan yang diinginkan
    const formattedSensors = sensorOrder
        .filter((key) => newData.sensor.hasOwnProperty(key))
        .map((key) => ({
            type: key,
            value: extractNumericValue(newData.sensor[key]),
            unit: sensorUnits[key] || "N/A",
        }));

    const existingIndex = mqttData.value.findIndex(
        (d) => d.node_id === newData.node_id
    );

    if (existingIndex !== -1) {
        formattedSensors.forEach((sensor) => {
            // Cari sensor berdasarkan type, bukan index
            const existingSensor = mqttData.value[existingIndex].sensors.find(
                (s) => s.type === sensor.type
            );
            if (existingSensor) {
                existingSensor.value = sensor.value;

                if (!existingSensor.data) {
                    existingSensor.data = Array(10).fill(sensor.value);
                } else {
                    existingSensor.data = [
                        ...existingSensor.data.slice(1),
                        sensor.value,
                    ];
                }

                // Temukan index sebenarnya untuk update chart
                const actualIndex = mqttData.value[
                    existingIndex
                ].sensors.findIndex((s) => s.type === sensor.type);
                setTimeout(() => {
                    updateOrCreateChart(
                        existingIndex,
                        actualIndex,
                        sensor.value
                    );
                }, 50);
            }
        });
    } else {
        const newDevice = {
            node_id: newData.node_id,
            timestamp: newData.timestamp,
            sensors: formattedSensors.map((sensor) => ({
                ...sensor,
                data: Array(10).fill(sensor.value),
            })),
        };

        mqttData.value.push(newDevice);
        const newIndex = mqttData.value.length - 1;

        formattedSensors.forEach((sensor, idx) => {
            setTimeout(() => {
                updateOrCreateChart(newIndex, idx, sensor.value);
            }, 100);
        });
    }
}

onMounted(() => {
    // Jika ada data awal, buat chart untuk masing-masing (data database)
    if (mqttData.value.length > 0 && activeNode.value === null) {
        // Definisikan urutan yang sama
        const sensorOrder = [
            "curah_hujan",
            "kecepatan_angin",
            "ketinggian_air",
            "tekanan_udara",
        ];

        mqttData.value.forEach((device, deviceIndex) => {
            // Urutkan sensor sesuai urutan yang diinginkan
            const sortedSensors = sensorOrder
                .map((type) => device.sensors.find((s) => s.type === type))
                .filter(Boolean);

            sortedSensors.forEach((sensor, sensorIndex) => {
                setTimeout(() => {
                    const chartId = `chart-${deviceIndex}-${sensorIndex}`;
                    const canvas = document.getElementById(chartId);
                    if (canvas) {
                        const ctx = canvas.getContext("2d");
                        if (ctx) {
                            const chart = new Chart(ctx, {
                                type: "line",
                                data: {
                                    labels: Array.from(
                                        { length: 10 },
                                        (_, i) => i + 1
                                    ),
                                    datasets: [
                                        {
                                            label: sensorLabel(sensor.type),
                                            data:
                                                sensor.data ||
                                                Array(10).fill(sensor.value),
                                            borderColor: getRandomColor(),
                                            borderWidth: 2,
                                            tension: 0.1,
                                            fill: false,
                                        },
                                    ],
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: { duration: 0 },
                                    scales: { y: { beginAtZero: true } },
                                },
                            });
                            charts.value.push({ id: chartId, chart });
                        }
                    }
                }, 100);
            });
        });
    }

    // Setup Echo
    const channel = window.Echo.channel("mqtt-sensor");
    channel.listen(".sensor.updated", (e) => {
        console.log("✅ Data diterima dari WebSocket:", e);
        handleMQTTData(e.payload);
    });
});

// Modifikasi onUnmounted
onUnmounted(() => {
    charts.value.forEach(({ chart }) => {
        try {
            chart.destroy();
        } catch (e) {
            console.error("Error destroying chart:", e);
        }
    });
    charts.value = [];
    mqttData.value = [];
    window.Echo.leave("mqtt-sensor");
});
</script>

<template>
    <Head title="Monitoring" />
    <div class="bg-blue-50">
        <div class="pt-20 w-[90%] mx-auto pb-10 min-h-screen">
            <div class="max-w-7xl mx-auto text-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                    Panel Real-Time Monitoring
                </h1>

                <p class="text-lg text-gray-600 max-w-4xl mx-auto">
                    Menampilkan data sensor bencana dari setiap alat pendeteksi
                    secara real-time.
                </p>
            </div>
            <!-- Kondisi 1: Belum ada node terdaftar -->
            <div
                v-if="monitoringStatus === 'no_nodes_registered'"
                class="text-gray-900 text-center flex flex-col items-center justify-center py-20"
            >
                <img
                    src="/assets/media/no_device1.png"
                    alt="Tidak ada perangkat terdaftar"
                    class="w-1/3 max-w-xs sm:max-w-sm md:max-w-md"
                />
                <p class="text-xl font-bold pt-4">
                    Tidak ada perangkat terdaftar dalam sistem.
                </p>
                <p class="text-gray-600">
                    Silakan hubungi administrator untuk mendaftarkan perangkat.
                </p>
            </div>

            <!-- Kondisi 2: Ada node tapi belum ada data realtime -->
            <div v-else-if="monitoringStatus === 'has_nodes_but_no_realtime'">
                <!-- <div class="flex justify-end pt-4 pb-1 gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <p class="p-1.5 rounded-full bg-red-500"></p>
                        <p>Offline</p>
                    </div>
                </div> -->
                <!-- Tabs untuk setiap device (sama persis dengan kondisi 3) -->
                <div role="tablist" class="tabs tabs-lifted">
                    <template
                        v-for="(device, index) in mqttData"
                        :key="device.node_id"
                    >
                        <input
                            type="radio"
                            :id="'tab-' + index"
                            name="device_tabs"
                            role="tab"
                            class="tab"
                            :aria-label="device.node_id"
                            :checked="index === 0"
                        />
                        <div
                            role="tabpanel"
                            class="tab-content bg-base-100 border-base-300 rounded-tr-xl rounded-br-xl rounded-bl-xl px-10 py-4"
                        >
                            <div
                                v-for="(sensor, sensorIndex) in device.sensors"
                                :key="sensor.type"
                                class="flex flex-wrap gap-[40px] justify-center items-center py-4"
                            >
                                <!-- Bagian kiri - Informasi sensor -->
                                <div class="grow-0">
                                    <div
                                        class="flex justify-center items-center h-full flex-col"
                                    >
                                        <h1 class="font-bold text-xl pb-2">
                                            {{ sensorLabel(sensor.type) }}
                                        </h1>
                                        <div
                                            class="stats stats-vertical shadow rounded-lg"
                                        >
                                            <div
                                                class="stat place-items-center"
                                            >
                                                <i
                                                    :class="
                                                        getSensorIcon(
                                                            sensor.type
                                                        )
                                                    "
                                                    :title="
                                                        sensorLabel(sensor.type)
                                                    "
                                                    class="text-8xl text-blue-500 py-4"
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
                                                        sensor.unit || "N/A"
                                                    }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bagian kanan - Grafik (ditampilkan tapi dengan data historis) -->
                                <div class="grow-[2]">
                                    <div
                                        class="card border-0 shadow text-base h-96 rounded-lg"
                                    >
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
            </div>

            <div v-else-if="monitoringStatus === 'has_nodes_and_realtime'">
                <!-- <div class="flex justify-start pt-4 pb-2 gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <p class="p-2 rounded-full bg-green-500"></p>
                        <p>Online</p>
                    </div>
                </div> -->
                <div role="tablist" class="tabs tabs-lifted">
                    <!-- Loop untuk setiap device (titik pantau) -->
                    <template
                        v-for="(device, index) in mqttData"
                        :key="device.node_id"
                    >
                        <input
                            type="radio"
                            :id="'tab-' + index"
                            name="device_tabs"
                            role="tab"
                            class="tab"
                            :aria-label="device.node_id"
                            :checked="index === 0"
                        />
                        <div
                            role="tabpanel"
                            class="tab-content bg-base-100 border-base-300 rounded-tr-xl rounded-br-xl rounded-bl-xl px-10 py-4"
                        >
                            <div
                                v-for="(sensor, sensorIndex) in device.sensors"
                                :key="sensor.type"
                                class="flex flex-wrap gap-[40px] justify-center items-center py-4"
                            >
                                <!-- Loop untuk setiap sensor dalam node -->
                                <div class="grow-0">
                                    <div
                                        class="flex justify-center items-center h-full flex-col"
                                    >
                                        <h1 class="font-bold text-xl pb-2">
                                            {{ sensorLabel(sensor.type) }}
                                        </h1>
                                        <div
                                            class="stats stats-vertical shadow rounded-lg"
                                        >
                                            <div
                                                class="stat place-items-center"
                                            >
                                                <i
                                                    :class="
                                                        getSensorIcon(
                                                            sensor.type
                                                        )
                                                    "
                                                    :title="
                                                        sensorLabel(sensor.type)
                                                    "
                                                    class="text-8xl text-blue-500 py-4"
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
                                                        sensor.unit || "N/A"
                                                    }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grafik Sensor -->
                                <div class="grow-[2]">
                                    <div
                                        class="card border-0 shadow text-base h-96 rounded-lg"
                                    >
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
            </div>
            <!-- Fallback kondisi tidak terduga -->
            <div
                v-else
                class="text-gray-900 text-center flex flex-col items-center justify-center py-20"
            >
                <p class="text-xl font-bold pt-4">
                    Status monitoring tidak dikenali.
                </p>
            </div>
        </div>
    </div>
</template>
