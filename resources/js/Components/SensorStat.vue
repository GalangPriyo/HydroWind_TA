<script setup>
const props = defineProps({
    sensorType: String,
    sensorValue: Number,
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

const sensorUnit = (name) => {
    const units = {
        kecepatan_angin: "km/jam",
        ketinggian_air: "cm",
        curah_hujan: "mm",
        tekanan_udara: "hPa",
    };
    return units[name] || "N/A";
};

const getSensorIcon = (name) => {
    const icons = {
        kecepatan_angin: "fas fa-wind",
        ketinggian_air: "fas fa-water",
        curah_hujan: "fas fa-cloud-showers-heavy",
        tekanan_udara: "fas fa-temperature-half",
    };
    return icons[name] || "fas fa-circle-xmark";
};
</script>

<template>
    <div class="flex justify-center items-center h-[350px] flex-col">
        <div
            class="stats stats-vertical border border-gray-200 rounded-lg w-full sm:w-auto h-full"
        >
            <div class="stat place-items-center">
                <div
                    class="flex items-center justify-center w-36 h-36 sm:w-48 sm:h-48 rounded-full bg-blue-100 border-2 border-blue-500"
                >
                    <i
                        :class="getSensorIcon(sensorType)"
                        :title="sensorLabel(sensorType)"
                        class="text-7xl sm:text-8xl text-blue-500"
                    />
                </div>
            </div>
            <div class="stat place-items-center w-full sm:w-80">
                <div class="stat-value text-5xl">
                    {{ sensorValue || 0 }}
                </div>
                <div class="stat-desc text-sm sm:text-base">
                    Diukur dalam satuan,
                    <span class="font-bold"
                        >{{ sensorUnit(sensorType) || "N/A" }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
