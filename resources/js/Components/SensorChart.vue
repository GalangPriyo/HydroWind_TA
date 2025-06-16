<script setup>
import { onMounted, ref, watch } from "vue";
import Chart from "chart.js/auto";

const props = defineProps({
    sensorType: String,
    sensorData: Array,
    sensorValue: Number,
    index: Number,
    sensorIndex: Number,
});

const chartInstance = ref(null);

// const getRandomColor = () => {
//     const r = Math.floor(Math.random() * 256);
//     const g = Math.floor(Math.random() * 256);
//     const b = Math.floor(Math.random() * 256);
//     return `rgb(${r}, ${g}, ${b})`;
// };

const sensorLabel = (name) => {
    const labels = {
        kecepatan_angin: "Kecepatan Angin",
        ketinggian_air: "Ketinggian Air Sungai",
        curah_hujan: "Curah Hujan",
        tekanan_udara: "Tekanan Udara",
    };
    return labels[name] || "Sensor Tidak Dikenal";
};

onMounted(() => {
    const canvas = document.getElementById(
        `chart-${props.index}-${props.sensorIndex}`
    );
    if (!canvas) return;

    const ctx = canvas.getContext("2d");
    if (!ctx) return;

    chartInstance.value = new Chart(ctx, {
        type: "line",
        data: {
            labels: Array.from({ length: 10 }, (_, i) => ""),
            datasets: [
                {
                    label: sensorLabel(props.sensorType),
                    data: props.sensorData || Array(10).fill(props.sensorValue),
                    borderColor: "#2b7fff",
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true,
                    backgroundColor: "rgba(219, 234, 254, 0.4)",
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
});

watch(
    () => props.sensorData,
    (newData) => {
        if (chartInstance.value && newData) {
            chartInstance.value.data.datasets[0].data = newData;
            chartInstance.value.update();
        }
    },
    { deep: true }
);

watch(
    () => props.sensorValue,
    (newValue) => {
        if (chartInstance.value && newValue !== undefined) {
            const currentData = chartInstance.value.data.datasets[0].data;
            const newData = [...currentData.slice(1), newValue];
            chartInstance.value.data.datasets[0].data = newData;
            chartInstance.value.update();
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
                :id="`chart-${index}-${sensorIndex}`"
                class="w-full h-full"
            ></canvas>
        </div>
    </div>
</template>
s
