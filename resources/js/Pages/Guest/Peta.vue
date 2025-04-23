<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { onMounted, onBeforeUnmount } from "vue";
import { usePage } from "@inertiajs/vue3";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

import { connectMQTT, removeMQTTHandler } from "@/mqtt/mqttClient";

defineOptions({ layout: GuestLayout });

const { props } = usePage(); // Data dari backend
let map = null;
const liveMarkers = {}; // Simpan marker per node_id

// Handler data dari MQTT
function onMQTTMessage(data) {
    if (!data?.node_id || !data?.gps) return;

    const { node_id, gps, angin } = data;

    if (liveMarkers[node_id]) {
        map.removeLayer(liveMarkers[node_id]);
    }

    // Bisa tambahkan icon custom nanti kalau mau
    const icon = createArrowIcon(angin?.derajat || 0);

    const marker = L.marker([gps.latitude, gps.longitude], { icon }).addTo(map)
        .bindPopup(`
        <b>${node_id}</b><br>
        Lat: ${gps.latitude}, Lng: ${gps.longitude}<br>
        Arah angin: ${angin?.arah || "-"} (${angin?.derajat || "-"}°)
    `);

    liveMarkers[node_id] = marker;
}

function createArrowIcon(angle) {
    return L.divIcon({
        className: "custom-arrow-icon",
        html: `
            <img 
                src="/assets/media/arrow.png" 
                style="
                    width: 32px;
                    height: 32px;
                    transform: rotate(${angle}deg);
                    transform-origin: center center;
                    display: block;
                " 
                crossorigin="anonymous"
            />
        `,
        iconSize: [32, 32],
        iconAnchor: [16, 16],
    });
}

onMounted(() => {
    map = L.map("map", {
        zoomControl: false,
    }).setView([-7.05294, 110.623819], 17);

    // Base layers
    const osmLayer = L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            attribution: "&copy; OpenStreetMap contributors",
        }
    );

    const esriLayer = L.tileLayer(
        "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
        {
            attribution:
                "&copy; Esri &mdash; Source: Esri, Maxar, Earthstar Geographics",
        }
    );

    const googleSatellite = L.tileLayer(
        "http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}",
        {
            subdomains: ["mt0", "mt1", "mt2", "mt3"],
            attribution: "&copy; Google Maps",
        }
    );

    const googleRoadmap = L.tileLayer(
        "http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}",
        {
            subdomains: ["mt0", "mt1", "mt2", "mt3"],
            attribution: "&copy; Google Maps",
        }
    );

    googleRoadmap.addTo(map);

    const baseMaps = {
        "Google Roadmap": googleRoadmap,
        OpenStreetMap: osmLayer,
        "Esri World Imagery": esriLayer,
        "Google Satellite": googleSatellite,
    };

    L.control.layers(baseMaps, null, { position: "topleft" }).addTo(map);
    L.control.zoom({ position: "bottomleft" }).addTo(map);

    // Tambahkan marker dari props (data awal)
    props.devices.forEach((device) => {
        L.marker([device.latitude, device.longitude])
            .addTo(map)
            .bindPopup(
                `<b>${device.name}</b><br>Lat: ${device.latitude}, Lng: ${device.longitude}`
            );
    });

    // Mulai koneksi MQTT dan listen data masuk
    connectMQTT(onMQTTMessage);
});

onBeforeUnmount(() => {
    // Bersihkan handler jika component ditutup
    removeMQTTHandler(onMQTTMessage);
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-b from-blue-200 to-cyan-200">
        <p class="text-center font-bold text-2xl sm:text-3xl pb-6 pt-20">
            Peta Lokasi Penempatan Alat
        </p>
        <div class="card bg-base-100 card-sm shadow-sm w-[90%] mx-auto">
            <div class="card-body p-0">
                <div
                    id="map"
                    class="w-full rounded-xl z-0"
                    style="height: 75vh"
                ></div>
            </div>
        </div>
    </div>
</template>
