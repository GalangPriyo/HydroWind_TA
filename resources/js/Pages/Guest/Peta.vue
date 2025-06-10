<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { onMounted, onBeforeUnmount } from "vue";
import { usePage, Head } from "@inertiajs/vue3";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import Echo from "laravel-echo";

defineOptions({ layout: GuestLayout });

const { props } = usePage(); // Data dari backend

console.log("Device meta from props:", props.devices);

let map = null;
const liveMarkers = {}; // Simpan marker per node_id
const deviceMeta = {}; // Simpan name & location per node_id
const sensorData = {}; // Simpan data sensor per node_id

// Simpan metadata device dari props
props.devices.forEach((device) => {
    deviceMeta[device.node_id] = {
        node_id: device.node_id,
        name: device.name,
        location: device.location,
        latitude: parseFloat(device.latitude),
        longitude: parseFloat(device.longitude),
    };
});

// Tampilkan marker awal dari database
function showInitialMarkers() {
    for (const node_id in deviceMeta) {
        const device = deviceMeta[node_id];

        // Skip jika lat/lng invalid
        if (isNaN(device.latitude) || isNaN(device.longitude)) continue;

        const marker = L.marker([device.latitude, device.longitude])
            .addTo(map)
            .bindPopup(
                `
                
                <div class="custom-popup">
        <div class="popup-header">
            <h3>${device.name}</h3>
            <span class="device-id">ID: ${device.node_id} </span>
        </div>
        
        <div class="popup-body">
            <div class="info-section">
                <div class="info-row">
                    <i class="fas fa-map-marker-alt" style="margin-right: 14px; margin-left: 2px; margin-top: 3px"></i>
                    <span>${device.location}</span>
                </div>
                <div class="info-row">
                    <i class="fas fa-map-pin" style="margin-top: 2px"></i>
                    <span>${device.latitude}, ${device.longitude}</span>
                </div>
            </div>
            <div>
                <p style="margin:0; text-align:center;">Belum Ada Data</p>
            </div>
        </div>
        
        <div class="popup-footer">
    <small>Terakhir update: ${new Date().toLocaleString("id-ID", {
        timeZone: "Asia/Jakarta",
    })}</small>
</div>

    </div>
                `
            );

        liveMarkers[node_id] = marker;
    }
}

// Tangani pesan dari MQTT GPS
function onMQTTGps(data) {
    if (!data?.node_id || !data?.gps || !deviceMeta[data.node_id]) return;

    const { node_id, gps, angin } = data;
    const lat = parseFloat(gps.latitude);
    const lng = parseFloat(gps.longitude);
    if (isNaN(lat) || isNaN(lng)) return;

    const device = deviceMeta[node_id];
    const icon = createArrowIcon(angin?.derajat || 0);

    if (liveMarkers[node_id]) {
        map.removeLayer(liveMarkers[node_id]);
    }

    const sensor = sensorData[node_id] || {};

    const marker = L.marker([lat, lng], { icon }).addTo(map).bindPopup(`
    <div class="custom-popup">
        <div class="popup-header">
            <h3>${device.name}</h3>
            <span class="device-id">ID: ${node_id}</span>
        </div>
        
        <div class="popup-body">
            <div class="info-section">
                <div class="info-row">
                    <i class="fas fa-map-marker-alt" style="margin-right: 14px; margin-left: 2px; margin-top: 3px"></i>
                    <span>${device.location}</span>
                </div>
                <div class="info-row">
                    <i class="fas fa-map-pin" style="margin-top: 2px"></i>
                    <span>${lat.toFixed(6)}, ${lng.toFixed(6)}</span>
                </div>
            </div>
            
            <div class="sensor-section">
                <div class="sensor-item arah">
                    <div class="sensor-title">Arah Angin</div>
                    <div class="sensor-value">
                        ${angin?.arah || "-"} (${angin?.derajat || "-"}°)
                    </div>
                </div>

                <div class="sensor-item wind">
                    <div class="sensor-title">Kecepatan Angin</div>
                    <div class="sensor-value">${
                        sensor.kecepatan_angin || "-"
                    }</div>
                </div>
                
                <div class="sensor-item water">
                    <div class="sensor-title">Ketinggian Air</div>
                    <div class="sensor-value">${
                        sensor.ketinggian_air || "-"
                    }</div>
                </div>
                
                <div class="sensor-item rain">
                    <div class="sensor-title">Curah Hujan</div>
                    <div class="sensor-value">${sensor.curah_hujan || "-"}</div>
                </div>
            </div>
        </div>
        
        <div class="popup-footer">
    <small>Terakhir update: ${new Date().toLocaleString("id-ID", {
        timeZone: "Asia/Jakarta",
    })}</small>
</div>

    </div>
`);

    liveMarkers[node_id] = marker;
}

// Tangani pesan dari MQTT Sensor
function onMQTTSensor(data) {
    if (!data?.node_id || !data?.sensor || !deviceMeta[data.node_id]) return;

    // Simpan sensor terakhir
    sensorData[data.node_id] = data.sensor;

    // Perbarui popup jika marker sudah ada
    if (liveMarkers[data.node_id]) {
        const marker = liveMarkers[data.node_id];
        const device = deviceMeta[data.node_id];

        marker.setPopupContent(`
        <div class="custom-popup">
            <div class="popup-header">
                <h3>${device.name}</h3>
                <span class="device-id">ID: ${data.node_id}</span>
            </div>
            
            <div class="popup-body">
                <div class="info-section">
                    <div class="info-row">
                        <i class="fas fa-map-marker-alt" style="margin-right: 14px; margin-left: 2px; margin-top: 3px"></i>
                        <span>${device.location}</span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-map-pin" style="margin-top: 2px"></i>
                        <span>${marker.getLatLng().lat.toFixed(6)}, ${marker
            .getLatLng()
            .lng.toFixed(6)}</span>
                    </div>
                </div>
                
                <div class="sensor-section">
                    <div class="sensor-item arah">
                        <div class="sensor-title">Arah Angin</div>
                        <div class="sensor-value">
                            ${data.angin?.arah || "-"} (${
            data.angin?.derajat || "-"
        }°)
                        </div>
                    </div>

                    <div class="sensor-item wind">
                        <div class="sensor-title">Kecepatan Angin</div>
                        <div class="sensor-value">${
                            data.sensor.kecepatan_angin || "-"
                        }</div>
                    </div>
                    
                    <div class="sensor-item water">
                        <div class="sensor-title">Ketinggian Air</div>
                        <div class="sensor-value">${
                            data.sensor.ketinggian_air || "-"
                        }</div>
                    </div>
                    
                    <div class="sensor-item rain">
                        <div class="sensor-title">Curah Hujan</div>
                        <div class="sensor-value">${
                            data.sensor.curah_hujan || "-"
                        }</div>
                    </div>
                </div>
            </div>
            
            <div class="popup-footer">
                <small>Terakhir update: ${new Date().toLocaleString("id-ID", {
                    timeZone: "Asia/Jakarta",
                    hour: "2-digit",
                    minute: "2-digit",
                    second: "2-digit",
                    day: "2-digit",
                    month: "2-digit",
                    year: "numeric",
                })}</small>
            </div>
        </div>
    `);
    }
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
    map = L.map("map", { zoomControl: false }).setView(
        [-7.05294, 110.623819],
        17
    );

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

    showInitialMarkers();

    window.Echo.channel("mqtt-gps").listen(".map.updated", (e) => {
        console.log("Received real-time GPS data:", e);
        onMQTTGps(e.payload);
    });

    window.Echo.channel("mqtt-sensor").listen(".sensor.updated", (e) => {
        console.log("Received real-time sensor data:", e);
        onMQTTSensor(e.payload);
    });
});

onBeforeUnmount(() => {
    window.Echo.leave("mqtt-gps");
    window.Echo.leave("mqtt-sensor");
});
</script>

<template>
    <Head title="Peta" />
    <div class="min-h-screen bg-blue-50">
        <div class="pt-20 pb-4 px-4 sm:px-6">
            <div class="max-w-7xl mx-auto text-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                    Peta Lokasi Penempatan Alat
                </h1>

                <p class="text-lg text-gray-600 max-w-4xl mx-auto">
                    Menampilkan posisi alat deteksi bencana aktif berdasarkan
                    koordinat terkini.
                </p>
            </div>
        </div>

        <div class="card bg-base-100 card-sm shadow-sm w-[90%] mx-auto">
            <div class="card-body p-0">
                <div
                    id="map"
                    class="w-full rounded-xl z-0 border-2 border-blue-700 h-[68vh] md:h-[70vh] lg:h-[72vh] xl:h-[73vh] 2xl:h-[74vh]"
                ></div>
            </div>
        </div>
    </div>
</template>

<style>
.custom-popup {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    width: 280px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.popup-header {
    background: #04133b;
    color: white;
    padding: 12px 15px;
    position: relative;
}

.popup-header h3 {
    margin-bottom: 2px;
    font-size: 18px;
    font-weight: 600;
}

.device-id {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 12px;
    margin-top: 4px;
}

.popup-body {
    padding: 15px;
    background: white;
}

.info-section {
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.info-row {
    display: flex;
    align-items: start;
    margin-bottom: 8px;
    font-size: 14px;
}

.info-row i {
    margin-right: 10px;
    color: #2b7fff;
    width: 16px;
    text-align: center;
}

.sensor-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.sensor-item {
    padding: 10px;
    border-radius: 8px;
    background: #dbeafe;
}

.sensor-title {
    font-size: 12px;
    color: #555;
    margin-bottom: 5px;
    font-weight: 500;
}

.sensor-value {
    font-weight: 600;
    font-size: 14px;
}

.sensor-item.wind .sensor-value {
    display: flex;
    justify-content: space-between;
}

.popup-footer {
    background: #f8f9fa;
    padding: 8px 15px;
    font-size: 11px;
    color: #666;
    text-align: right;
    border-top: 1px solid #eee;
}
</style>
