<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

defineOptions({ layout: GuestLayout });

const { props } = usePage(); // Mengambil data devices dari backend

onMounted(() => {
    let map = L.map("map", {
        zoomControl: false, // Matikan kontrol zoom default
    }).setView([-7.05294, 110.623819], 17);

    // **Tile Layers**
    let osmLayer = L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            attribution: "&copy; OpenStreetMap contributors",
        }
    );

    let esriLayer = L.tileLayer(
        "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
        {
            attribution:
                "&copy; Esri &mdash; Source: Esri, Maxar, Earthstar Geographics",
        }
    );

    let googleSatellite = L.tileLayer(
        "http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}",
        {
            subdomains: ["mt0", "mt1", "mt2", "mt3"],
            attribution: "&copy; Google Maps",
        }
    );

    let googleRoadmap = L.tileLayer(
        "http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}",
        {
            subdomains: ["mt0", "mt1", "mt2", "mt3"],
            attribution: "&copy; Google Maps",
        }
    );

    // **Set Default Layer**
    googleRoadmap.addTo(map);

    // **Layer Control**
    let baseMaps = {
        "Google Roadmap": googleRoadmap,
        OpenStreetMap: osmLayer,
        "Esri World Imagery": esriLayer,
        "Google Satellite": googleSatellite,
    };

    L.control.layers(baseMaps, null, { position: "topleft" }).addTo(map);
    L.control.zoom({ position: "bottomleft" }).addTo(map);

    // **Tambahkan Marker dari Database**
    props.devices.forEach((device) => {
        L.marker([device.latitude, device.longitude])
            .addTo(map)
            .bindPopup(
                `<b>${device.name}</b><br>Lat: ${device.latitude}, Lng: ${device.longitude}`
            );
    });

    // **Nonaktifkan zoom dengan scroll**
    // map.scrollWheelZoom.disable();
});
</script>

<template>
    <p class="text-center font-bold text-2xl sm:text-3xl pb-6 mt-20">
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
</template>
