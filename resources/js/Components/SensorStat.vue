<script setup>
const props = defineProps({
    sensorType: String,
    sensorValue: Number,
    unit: String,
});

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
        kecepatan_angin: "fas fa-wind",
        ketinggian_air: "fas fa-water",
        curah_hujan: "fas fa-cloud-showers-heavy",
        tekanan_udara: "fas fa-tachometer-alt",
    };
    return icons[name] || "fas fa-circle-xmark";
};
</script>

<template>
    <div class="flex justify-center items-center h-full flex-col">
        <h1 class="font-bold text-lg sm:text-xl pb-2">
            {{ sensorLabel(sensorType) }}
        </h1>
        <div class="stats stats-vertical shadow rounded-lg w-full sm:w-auto">
            <div class="stat place-items-center">
                <i
                    :class="getSensorIcon(sensorType)"
                    :title="sensorLabel(sensorType)"
                    class="text-7xl sm:text-8xl text-blue-500 py-3 sm:py-4"
                />
            </div>
            <div class="stat place-items-center w-full sm:w-80">
                <div class="stat-value text-4xl sm:text-5xl">
                    {{ sensorValue || 0 }}
                </div>
                <div class="stat-desc text-sm sm:text-base">
                    Diukur dalam satuan,
                    <span class="font-bold">{{ unit || "N/A" }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
