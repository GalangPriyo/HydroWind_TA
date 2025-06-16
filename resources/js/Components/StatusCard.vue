<template>
    <div class="w-full max-w-xs">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-primary py-4 px-6">
                <h2
                    class="text-xl font-bold text-white flex items-center justify-center gap-2"
                >
                    <i
                        :class="'fa-solid ' + icon + ' text-blue-500'"
                        aria-hidden="true"
                    ></i>
                    {{ title }}
                </h2>
            </div>
            <div class="px-6 pb-6">
                <div class="flex justify-center">
                    <component
                        :is="statusClasses.component"
                        class="w-8 h-8"
                    ></component>
                </div>
                <div class="text-center mb-6">
                    <span
                        :class="
                            'inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold shadow-md ' +
                            statusClasses.bg +
                            ' ' +
                            statusClasses.text
                        "
                    >
                        {{ statusText }}
                    </span>
                </div>
                <div
                    :class="
                        'rounded-lg p-4 border ' +
                        statusClasses.cardBg +
                        ' ' +
                        statusClasses.cardBorder
                    "
                >
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-500 ml-0.5">
                            <i class="fa-solid fa-location-dot mr-2"></i>Lokasi
                        </span>
                        <span class="font-semibold">{{ data.location }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-500">
                            <i class="fa-solid fa-gauge mr-2"></i>Nilai
                        </span>
                        <span
                            :class="
                                'text-lg font-bold ' + statusClasses.cardText
                            "
                        >
                            {{ formattedValue }}
                        </span>
                    </div>
                    <div
                        :class="
                            'w-full rounded-full h-2.5 ' +
                            statusClasses.progress
                        "
                    ></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Safe from "@/Components/Safe.vue";
import Danger from "@/Components/Danger.vue";
import Warning from "@/Components/Warning.vue";

export default {
    props: ["type", "data"],
    components: { Safe, Danger, Warning },
    computed: {
        statusClasses() {
            const statusConfig = {
                aman: {
                    bg: "bg-cyan-400",
                    text: "text-white",
                    component: Safe,
                    cardBg: "bg-cyan-50",
                    cardBorder: "border-cyan-200",
                    cardText: "text-cyan-700",
                    progress: "bg-cyan-200",
                },
                waspada: {
                    bg: "bg-amber-400",
                    text: "text-white",
                    component: Warning,
                    cardBg: "bg-amber-50",
                    cardBorder: "border-amber-200",
                    cardText: "text-amber-700",
                    progress: "bg-amber-200",
                },
                bahaya: {
                    bg: "bg-red-500",
                    text: "text-white",
                    component: Danger,
                    cardBg: "bg-red-50",
                    cardBorder: "border-red-200",
                    cardText: "text-red-700",
                    progress: "bg-red-200",
                },
            };

            if (
                !this.data ||
                !this.data.status ||
                !statusConfig[this.data.status]
            ) {
                return statusConfig.aman;
            }

            return statusConfig[this.data.status];
        },
        icon() {
            return {
                curah_hujan: "fa-cloud-showers-heavy",
                ketinggian_air: "fa-water",
                kecepatan_angin: "fa-wind",
                tekanan_udara: "fa-temperature-half",
            }[this.type];
        },
        title() {
            return {
                curah_hujan: "Status Curah Hujan",
                ketinggian_air: "Status Ketinggian Air",
                kecepatan_angin: "Status Kecepatan Angin",
                tekanan_udara: "Status Tekanan Udara",
            }[this.type];
        },
        formattedValue() {
            return this.formatValue(this.data.value, this.type);
        },
        statusText() {
            return this.data.status.toUpperCase();
        },
    },
    methods: {
        formatValue(value, type) {
            const units = {
                curah_hujan: "mm",
                ketinggian_air: "cm",
                kecepatan_angin: "km/jam",
                tekanan_udara: "hPa",
            };

            // if (type === "kecepatan_angin") {
            //     return `${Math.round(value * 3.6)} ${units[type]}`;
            // }

            return `${value} ${units[type]}`;
        },
    },
};
</script>
