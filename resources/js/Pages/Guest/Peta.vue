<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";

defineOptions({ layout: GuestLayout });

const { props } = usePage(); // Ambil props dari Inertia

onMounted(() => {
    const apiKey = props.googleMapsApiKey; // Ambil API Key dari props
    const script = document.createElement("script");
    script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);

    window.initMap = function () {
        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: -7.051049, lng: 110.625776 },
            zoom: 15,
        });

        // Data dummy node sensor
        const nodes = [
            {
                id: 1,
                latitude: -7.053919,
                longitude: 110.622893,
                arah_angin: 90,
            },
            {
                id: 2,
                latitude: -7.051581,
                longitude: 110.623795,
                arah_angin: 180,
            },
        ];

        nodes.forEach((node) => {
            const { latitude, longitude, arah_angin } = node;

            new google.maps.Marker({
                position: { lat: latitude, lng: longitude },
                map: map,
                icon: {
                    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                    scale: 5,
                    rotation: arah_angin, // Rotasi sesuai arah angin
                    strokeColor: "red",
                },
                title: `Node ${node.id}\nArah Angin: ${arah_angin}°`,
            });
        });
    };
});
</script>

<template>
    <div id="map" class="min-h-screen"></div>
</template>
