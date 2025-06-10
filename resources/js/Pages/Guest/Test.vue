<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { onMounted, nextTick, ref, onUnmounted } from "vue";
import Chart from "chart.js/auto";
import Echo from "laravel-echo";
import { Head } from "@inertiajs/vue3";

defineOptions({ layout: GuestLayout });

const props = defineProps({
    registeredNodeIds: Array,
});

const validNodeIds = ref(props.registeredNodeIds || []);

const mqttData = ref([]); // Data dari MQTT
const charts = []; // Referensi chart berdasarkan ID

const sensorLabel = (name) => {
    const labels = {
        curah_hujan: "Intensitas Hujan",
        ketinggian_air: "Ketinggian Air Sungai",
        kecepatan_angin: "Kecepatan Angin",
        wind_direction: "Arah Angin",
    };
    return labels[name] || "Sensor Tidak Dikenal";
};

const getSensorImage = (name) => {
    const images = {
        curah_hujan: "/assets/media/hujan2.png",
        ketinggian_air: "/assets/media/sungai.png",
        kecepatan_angin: "/assets/media/angin.png",
        wind_direction: "/assets/media/kompas.png",
    };
    return images[name] || "/assets/media/default.png";
};

const getRandomColor = () => {
    const r = Math.floor(Math.random() * 256);
    const g = Math.floor(Math.random() * 256);
    const b = Math.floor(Math.random() * 256);
    return `rgb(${r}, ${g}, ${b})`;
};

const updateOrCreateChart = async (formattedData) => {
    await nextTick();

    const deviceIndex = mqttData.value.findIndex(
        (d) => d.node_id === formattedData.node_id
    );
    if (deviceIndex === -1) return;

    const device = mqttData.value[deviceIndex];

    device.sensors.forEach((sensor, sensorIndex) => {
        const chartId = `chart-${deviceIndex}-${sensorIndex}`;
        const ctx = document.getElementById(chartId)?.getContext("2d");
        if (!ctx) return;

        let chartObj = charts.find((c) => c.id === chartId);

        if (!chartObj) {
            const newChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: Array(10).fill(""),
                    datasets: [
                        {
                            label: sensorLabel(sensor.type),
                            data: Array(10).fill(sensor.value),
                            borderColor: getRandomColor(),
                            borderWidth: 2,
                        },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false },
            });

            charts.push({ id: chartId, chart: newChart });
        } else {
            const chart = chartObj.chart;
            const dataset = chart.data.datasets[0];

            dataset.data.shift();
            dataset.data.push(sensor.value);
            chart.update();
        }
    });
};

// Callback saat data dari MQTT masuk
function handleMQTTData(newData) {
    if (
        !newData ||
        typeof newData.sensor !== "object" ||
        newData.sensor === null
    ) {
        return; // abaikan jika bukan data sensor
    }

    // // 💡 Filter node_id yang tidak terdaftar
    if (!validNodeIds.value.includes(newData.node_id)) {
        console.warn("Data dari node_id tidak dikenal:", newData.node_id);
        return;
    }

    const sensorUnits = {
        kecepatan_angin: "m/s",
        ketinggian_air: "cm",
        curah_hujan: "mm",
        tekanan_udara: "mb",
    };

    const formattedSensors = Object.keys(newData.sensor).map((key) => ({
        type: key,
        value: parseFloat(newData.sensor[key]) || 0,
        unit: sensorUnits[key] || "N/A",
    }));

    const formattedData = {
        node_id: newData.node_id,
        timestamp: newData.timestamp,
        sensors: formattedSensors,
    };

    const existingIndex = mqttData.value.findIndex(
        (d) => d.node_id === formattedData.node_id
    );

    if (existingIndex !== -1) {
        formattedSensors.forEach((sensor, idx) => {
            const existingSensor = mqttData.value[existingIndex].sensors[idx];
            existingSensor.value = sensor.value;

            if (!existingSensor.data) {
                existingSensor.data = Array(10).fill(sensor.value);
            }

            existingSensor.data.shift();
            existingSensor.data.push(sensor.value);
        });
    } else {
        mqttData.value.push({
            ...formattedData,
            sensors: formattedSensors.map((sensor) => ({
                ...sensor,
                data: Array(10).fill(sensor.value),
            })),
        });
    }

    updateOrCreateChart(formattedData);
}

onMounted(() => {
    console.log("Echo object:", window.Echo);

    // Listen untuk event
    const channel = window.Echo.channel("mqtt-sensor");

    channel.listen(".sensor.updated", (e) => {
        console.log("✅ Data diterima dari WebSocket:", e);
        handleMQTTData(e.payload);
    });

    // Debug channel subscription
    channel.subscribed(() => {
        console.log("✅ Successfully subscribed to mqtt-sensor channel");
    });
});

onUnmounted(() => {
    // Bersihkan charts
    charts.forEach(({ chart }) => chart.destroy());
    charts.length = 0;

    mqttData.value = [];

    // Hentikan listening event
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

            <div>
                <div
                    v-if="mqttData.length > 0"
                    role="tablist"
                    class="tabs tabs-lifted"
                >
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
                                                <img
                                                    :src="
                                                        getSensorImage(
                                                            sensor.type
                                                        )
                                                    "
                                                    :alt="
                                                        sensorLabel(sensor.type)
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

                <!-- Tampilkan pesan jika tidak ada data -->
                <div
                    v-else
                    class="text-gray-900 text-center flex flex-col items-center justify-center py-20"
                >
                    <img
                        src="/assets/media/no_device1.png"
                        alt="Tidak ada perangkat"
                        class="w-1/3 max-w-xs sm:max-w-sm md:max-w-md"
                    />
                    <p class="text-xl font-bold pt-4">
                        Tidak ada perangkat tersambung.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
