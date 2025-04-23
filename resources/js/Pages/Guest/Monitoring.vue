<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { onMounted, nextTick, ref, onUnmounted } from "vue";
import Chart from "chart.js/auto";
import { connectMQTT, removeMQTTHandler } from "@/mqtt/mqttClient";

defineOptions({ layout: GuestLayout });

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
        typeof newData.sensors !== "object" ||
        newData.sensors === null
    ) {
        return; // abaikan jika bukan data sensor
    }
    const formattedSensors = Object.keys(newData.sensors).map((key) => ({
        type: newData.sensors[key].type,
        value: newData.sensors[key].value,
        unit: newData.sensors[key].unit,
    }));

    const formattedData = {
        node_id: newData.node_id,
        timestamp: newData.timestamp,
        sensors: formattedSensors,
        gps: newData.gps,
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
    connectMQTT(handleMQTTData); // Singleton connect
});

onUnmounted(() => {
    // Hapus semua chart
    charts.forEach(({ chart }) => chart.destroy());
    charts.length = 0;

    // Bersihkan data agar tidak numpuk
    mqttData.value = [];

    // Unregister handler dari singleton
    removeMQTTHandler(handleMQTTData);
});
</script>

<template>
    <div class="bg-gradient-to-b from-blue-200 to-cyan-200">
        <div class="pt-20 w-[90%] mx-auto pb-10 min-h-screen">
            <p class="text-center font-bold text-2xl sm:text-3xl pb-6">
                Panel Real-Time Monitoring
            </p>
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
