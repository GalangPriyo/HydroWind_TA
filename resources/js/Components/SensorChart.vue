<script setup>
import { onMounted, ref, watch, onBeforeUnmount, nextTick, markRaw } from "vue"; // Import markRaw
import Chart from "chart.js/auto";

const props = defineProps({
    sensorType: String,
    sensorData: Array,
    sensorValue: Number,
    index: Number,
    sensorIndex: Number,
});

const chartInstance = ref(null);
const chartCanvas = ref(null);

const sensorLabel = (name) => {
    const labels = {
        kecepatan_angin: "Kecepatan Angin",
        ketinggian_air: "Ketinggian Air Sungai",
        curah_hujan: "Curah Hujan",
        tekanan_udara: "Tekanan Udara",
    };
    return labels[name] || "Sensor Tidak Dikenal";
};

const initializeChart = () => {
    if (!chartCanvas.value) return;

    if (chartInstance.value) {
        chartInstance.value.destroy();
    }

    const ctx = chartCanvas.value.getContext("2d");
    if (!ctx) return;

    // Convert props data to plain JS arrays to avoid proxy issues on initialization
    const initialData = props.sensorData
        ? [...props.sensorData]
        : Array(10).fill(props.sensorValue || 0);

    // Use markRaw to prevent Vue from making the chart instance reactive
    chartInstance.value = markRaw(
        new Chart(ctx, {
            type: "line",
            data: {
                labels: Array.from({ length: 10 }, () => ""),
                datasets: [
                    {
                        label: sensorLabel(props.sensorType),
                        data: initialData, // Use the plain array
                        borderColor: "#2b7fff",
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true,
                        backgroundColor: "rgba(219, 234, 254, 0.4)",
                        pointBackgroundColor: "#2b7fff",
                        pointRadius: 3,
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
                        grid: {
                            display: true,
                            color: "rgba(0, 0, 0, 0.1)",
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                        labels: {
                            boxWidth: 12,
                        },
                    },
                    tooltip: {
                        enabled: true,
                        mode: "index",
                        intersect: false,
                    },
                },
            },
        })
    );
};

onMounted(() => {
    nextTick(() => {
        initializeChart();
    });
});

onBeforeUnmount(() => {
    if (chartInstance.value) {
        chartInstance.value.destroy();
        chartInstance.value = null;
    }
});

watch(
    () => [props.sensorData, props.sensorValue],
    ([newData, newValue]) => {
        if (!chartInstance.value) return;

        try {
            if (newData && Array.isArray(newData)) {
                // IMPORTANT: Convert reactive proxy to a plain array
                chartInstance.value.data.datasets[0].data = [...newData];
            } else if (newValue !== undefined) {
                // The current data from the chart is already a plain array
                const currentData = chartInstance.value.data.datasets[0].data;
                const updatedData = [...currentData.slice(1), newValue];
                chartInstance.value.data.datasets[0].data = updatedData;
            }

            chartInstance.value.update();
        } catch (error) {
            console.error("Error updating chart data:", error);
        }
    },
    { deep: true }
);

watch(
    () => props.sensorType,
    (newType) => {
        if (chartInstance.value && newType) {
            initializeChart();
        }
    }
);
</script>

<template>
    <div
        class="card border border-gray-200 text-base h-[350px] rounded-lg bg-white"
    >
        <div class="card-body items-center text-center p-2 sm:p-4 h-full">
            <canvas
                ref="chartCanvas"
                :id="`chart-${index}-${sensorIndex}`"
                class="w-full h-full"
            ></canvas>
        </div>
    </div>
</template>
