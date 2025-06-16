<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { onMounted, onBeforeUnmount } from "vue";
import { usePage, Head } from "@inertiajs/vue3";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import Echo from "laravel-echo";

defineOptions({ layout: GuestLayout });

const props = defineProps({
    devices: Array,
});

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
    props.devices.forEach((device) => {
        if (isNaN(device.latitude) || isNaN(device.longitude)) return;

        let sensorHtml = "";
        const availableSensors = [];

        if (device.sensors && device.sensors.length > 0) {
            device.sensors.forEach((sensor) => {
                if (sensor.latest_data) {
                    availableSensors.push(sensor.name);
                }
            });

            if (availableSensors.length > 0) {
                sensorHtml = '<div class="sensor-section">';

                if (availableSensors.includes("kecepatan_angin")) {
                    const kecepatanSensor = device.sensors.find(
                        (s) => s.name === "kecepatan_angin"
                    );
                    sensorHtml += `
    <div class="sensor-item wind">
        <div class="sensor-title">Kecepatan Angin</div>
        <div class="sensor-value">${
            kecepatanSensor.latest_data.value !== undefined &&
            kecepatanSensor.latest_data.value !== null
                ? kecepatanSensor.latest_data.value + " km/jam"
                : "-"
        }</div>
    </div>`;
                }

                if (availableSensors.includes("ketinggian_air")) {
                    const ketinggianSensor = device.sensors.find(
                        (s) => s.name === "ketinggian_air"
                    );
                    sensorHtml += `
    <div class="sensor-item water">
        <div class="sensor-title">Ketinggian Air</div>
        <div class="sensor-value">${
            ketinggianSensor.latest_data.value !== undefined &&
            ketinggianSensor.latest_data.value !== null
                ? ketinggianSensor.latest_data.value + " cm"
                : "-"
        }</div>
    </div>`;
                }

                if (availableSensors.includes("curah_hujan")) {
                    const curahHujanSensor = device.sensors.find(
                        (s) => s.name === "curah_hujan"
                    );
                    sensorHtml += `
    <div class="sensor-item rain">
        <div class="sensor-title">Curah Hujan</div>
        <div class="sensor-value">${
            curahHujanSensor.latest_data.value !== undefined &&
            curahHujanSensor.latest_data.value !== null
                ? curahHujanSensor.latest_data.value + " mm"
                : "-"
        }</div>
    </div>`;
                }

                if (availableSensors.includes("tekanan_udara")) {
                    const tekananSensor = device.sensors.find(
                        (s) => s.name === "tekanan_udara"
                    );
                    sensorHtml += `
    <div class="sensor-item pressure">
        <div class="sensor-title">Tekanan Udara</div>
        <div class="sensor-value">${
            tekananSensor.latest_data.value !== undefined &&
            tekananSensor.latest_data.value !== null
                ? tekananSensor.latest_data.value + " hPa"
                : "-"
        }</div>
    </div>`;
                }

                sensorHtml += "</div>";
            } else {
                sensorHtml =
                    '<p style="margin: 0; text-align: center;">Belum Ada Data</p>';
            }
        } else {
            sensorHtml =
                '<p style="margin: 0; text-align: center;">Belum Ada Data</p>';
        }

        // Buat marker dengan popupHtml
        const popupHtml = `
            <div class="custom-popup">
                <div class="popup-header">
                    <h3>${device.name}</h3>
                    <span class="device-id">ID: ${device.node_id}</span>
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
                    ${sensorHtml}
                </div>

                <div class="popup-footer">
                    <small>
                Terakhir diperbarui: ${
                    device.sensors?.[0]?.latest_data?.timestamp
                        ? (() => {
                              const d = new Date(
                                  device.sensors[0].latest_data.timestamp
                              );
                              const day = String(d.getDate()).padStart(2, "0");
                              const month = String(d.getMonth() + 1).padStart(
                                  2,
                                  "0"
                              );
                              const year = d.getFullYear();
                              const time = d.toTimeString().split(" ")[0];
                              return `${day}-${month}-${year} | ${time} WIB`;
                          })()
                        : "Tidak tersedia"
                }

            </small>
                </div>
            </div>
        `;

        const marker = L.marker([device.latitude, device.longitude])
            .addTo(map)
            .bindPopup(popupHtml);

        liveMarkers[device.node_id] = marker;
    });
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
    const sensor = sensorData[node_id] || {};

    if (liveMarkers[node_id]) {
        map.removeLayer(liveMarkers[node_id]);
    }

    // Daftar sensor yang tersedia (gabungan dari sensorData dan data angin)
    const availableSensors = Object.keys(sensor).filter(
        (key) => sensor[key] !== null
    );
    if (angin?.arah) availableSensors.push("arah_angin");

    let sensorHtml = "";

    if (availableSensors.length > 0) {
        sensorHtml = '<div class="sensor-section">';

        // Arah angin (dari data GPS)
        if (angin?.arah) {
            sensorHtml += `
            <div class="sensor-item arah">
                <div class="sensor-title">Arah Angin</div>
                <div class="sensor-value">
                    ${angin.arah} (${angin.derajat || "-"}°)
                </div>
            </div>`;
        }

        // Sensor lainnya (dari sensorData)
        if (availableSensors.includes("kecepatan_angin")) {
            sensorHtml += `
    <div class="sensor-item wind">
        <div class="sensor-title">Kecepatan Angin</div>
        <div class="sensor-value">${
            sensor.kecepatan_angin !== undefined &&
            sensor.kecepatan_angin !== null
                ? sensor.kecepatan_angin + " km/jam"
                : "-"
        }</div>
    </div>`;
        }

        if (availableSensors.includes("ketinggian_air")) {
            sensorHtml += `
    <div class="sensor-item water">
        <div class="sensor-title">Ketinggian Air</div>
        <div class="sensor-value">${
            sensor.ketinggian_air !== undefined &&
            sensor.ketinggian_air !== null
                ? sensor.ketinggian_air + " cm"
                : "-"
        }</div>
    </div>`;
        }

        if (availableSensors.includes("curah_hujan")) {
            sensorHtml += `
    <div class="sensor-item rain">
        <div class="sensor-title">Curah Hujan</div>
        <div class="sensor-value">${
            sensor.curah_hujan !== undefined && sensor.curah_hujan !== null
                ? sensor.curah_hujan + " mm"
                : "-"
        }</div>
    </div>`;
        }

        if (availableSensors.includes("tekanan_udara")) {
            sensorHtml += `
    <div class="sensor-item pressure">
        <div class="sensor-title">Tekanan Udara</div>
        <div class="sensor-value">${
            sensor.tekanan_udara !== undefined && sensor.tekanan_udara !== null
                ? sensor.tekanan_udara + " hPa"
                : "-"
        }</div>
    </div>`;
        }

        sensorHtml += "</div>";
    } else {
        sensorHtml =
            '<p style="margin: 0; text-align: center;">Belum Ada Data</p>';
    }

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
                ${sensorHtml}
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

        // Dapatkan daftar sensor yang tersedia
        const availableSensors = Object.keys(data.sensor).filter(
            (key) => data.sensor[key] !== null
        );

        let sensorHtml = "";

        if (availableSensors.length > 0) {
            sensorHtml = '<div class="sensor-section">';

            if (availableSensors.includes("arah_angin")) {
                sensorHtml += `
                <div class="sensor-item arah">
                    <div class="sensor-title">Arah Angin</div>
                    <div class="sensor-value">
                        ${data.angin?.arah || "-"} (${
                    data.angin?.derajat || "-"
                }°)
                    </div>
                </div>`;
            }

            if (availableSensors.includes("kecepatan_angin")) {
                sensorHtml += `
                <div class="sensor-item wind">
                    <div class="sensor-title">Kecepatan Angin</div>
                    <div class="sensor-value">${
                        data.sensor.kecepatan_angin || "-"
                    }</div>
                </div>`;
            }

            if (availableSensors.includes("ketinggian_air")) {
                sensorHtml += `
                <div class="sensor-item water">
                    <div class="sensor-title">Ketinggian Air</div>
                    <div class="sensor-value">${
                        data.sensor.ketinggian_air || "-"
                    }</div>
                </div>`;
            }

            if (availableSensors.includes("curah_hujan")) {
                sensorHtml += `
                <div class="sensor-item rain">
                    <div class="sensor-title">Curah Hujan</div>
                    <div class="sensor-value">${
                        data.sensor.curah_hujan || "-"
                    }</div>
                </div>`;
            }

            if (availableSensors.includes("tekanan_udara")) {
                sensorHtml += `
                <div class="sensor-item pressure">
                    <div class="sensor-title">Tekanan Udara</div>
                    <div class="sensor-value">${
                        data.sensor.tekanan_udara || "-"
                    }</div>
                </div>`;
            }

            sensorHtml += "</div>";
        } else {
            sensorHtml =
                '<p style="margin: 0; text-align: center;">Belum Ada Data</p>';
        }

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
                
                ${sensorHtml}
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

                <p class="text-base sm:text-lg text-gray-600 max-w-4xl mx-auto">
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

.sensor-item.arah {
    background: #dbeafe;
    grid-column: span 2;
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
