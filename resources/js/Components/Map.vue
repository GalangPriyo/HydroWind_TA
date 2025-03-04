<template>
    <div id="map" style="height: 500px"></div>
</template>

<script>
import L from "leaflet";
import "leaflet/dist/leaflet.css";

export default {
    props: {
        nodes: Array, // Data node GPS dari backend
    },
    mounted() {
        const map = L.map("map").setView([-6.1754, 106.8272], 10); // Default Jakarta

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "© OpenStreetMap contributors",
        }).addTo(map);

        // Tambahkan setiap node ke peta
        this.nodes.forEach((node) => {
            const { latitude, longitude, arah_angin } = node;

            // Buat ikon arah angin
            const windIcon = L.divIcon({
                html: `<div style="transform: rotate(${arah_angin}deg); font-size: 24px;">🡆</div>`, // Ikon panah berputar
                class: "wind-icon",
                iconSize: [24, 24],
            });

            // Tambahkan marker dengan ikon arah angin
            L.marker([latitude, longitude], { icon: windIcon })
                .addTo(map)
                .bindPopup(`Node ${node.id}<br>Arah Angin: ${arah_angin}°`);
        });
    },
};
</script>

<style>
.wind-icon {
    text-align: center;
    color: red;
}
</style>
