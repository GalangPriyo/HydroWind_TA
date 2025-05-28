const nodeAlertState = {};

export const cekDanKirimNotifikasi = (data) => {
    console.log(
        `[DEBUG] Dipanggil cekDanKirimNotifikasi untuk Node ${data.node_id}`,
        data
    );

    const nodeId = data.node_id || "unknown";

    // ✅ Tambahkan pengecekan: hanya proses node_id yang sesuai pola "Node-[angka]"
    const isValidNodeId = /^Node-\d+$/.test(nodeId);
    if (!isValidNodeId) {
        console.warn(
            `[ABORT] Node ID "${nodeId}" tidak sesuai pola "Node-[angka]", notifikasi tidak dikirim.`
        );
        return;
    }

    const now = new Date();
    const nowMs = now.getTime();

    // Konversi ke WIB
    const nowInWIB = new Date(
        now.toLocaleString("en-US", { timeZone: "Asia/Jakarta" })
    );
    const jam = nowInWIB.getHours();
    const menit = nowInWIB.getMinutes();
    const tanggal = nowInWIB.toISOString().split("T")[0];

    if (!nodeAlertState[nodeId]) {
        nodeAlertState[nodeId] = {
            lastAlertSent: 0,
            lastSafeStatusSentDate: null,
            isSending: false,
        };
    }

    const state = nodeAlertState[nodeId];
    const sensor = data.sensor;
    if (!sensor) return;

    const extractNumber = (value) => {
        if (typeof value === "string") {
            const num = parseFloat(value.replace(/[^\d.-]/g, ""));
            return isNaN(num) ? 0 : num;
        }
        return typeof value === "number" ? value : 0;
    };

    const batasBahaya = {
        curah_hujan: 1100,
        ketinggian_air: 1200,
        kecepatan_angin: 1300,
        tekanan_udara: 1400,
    };

    const batasWaspada = {
        curah_hujan: 500,
        ketinggian_air: 60,
        kecepatan_angin: 1,
        tekanan_udara: 800,
    };

    let status = "aman";
    let sensorTerpicu = [];
    let nilaiSensor = {};

    for (const key in batasBahaya) {
        const value = extractNumber(sensor[key]);

        if (value >= batasBahaya[key]) {
            status = "bahaya";
            sensorTerpicu.push(key);
            nilaiSensor[key] = value;
        } else if (value >= batasWaspada[key]) {
            if (status !== "bahaya") status = "waspada";
            sensorTerpicu.push(key);
            nilaiSensor[key] = value;
        }
    }

    const labelSensor = {
        curah_hujan: "Curah Hujan",
        ketinggian_air: "Ketinggian Air",
        kecepatan_angin: "Kecepatan Angin",
        tekanan_udara: "Tekanan Udara",
    };

    if ((status === "bahaya" || status === "waspada") && !state.isSending) {
        if (nowMs - state.lastAlertSent < 600000) return;
        state.lastAlertSent = nowMs;
        state.isSending = true;

        console.log(
            `[Kirim Alert] Node: ${nodeId}, Status: ${status}, Terpicu:`,
            sensorTerpicu
        );

        const sensorInfo = sensorTerpicu
            .map((key) => `${labelSensor[key]}: ${nilaiSensor[key]}`)
            .join(", ");

        fetch("/api/trigger-alert", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
            },
            body: JSON.stringify({
                node_id: nodeId,
                sensor_type: sensorTerpicu
                    .map((key) => labelSensor[key])
                    .join(", "),
                value: sensorInfo,
                status: status,
            }),
        })
            .then((res) => res.json())
            .then((res) => {
                console.log(`✅ Notifikasi [${nodeId}] ${status}:`, res);
            })
            .catch((err) => {
                console.error(`❌ Gagal kirim notifikasi [${nodeId}]:`, err);
            })
            .finally(() => {
                state.isSending = false;
            });
    } else if (
        status === "aman" &&
        jam === 12 &&
        menit === 0 &&
        state.lastSafeStatusSentDate !== tanggal
    ) {
        state.lastSafeStatusSentDate = tanggal;

        fetch("/api/trigger-alert", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
            },
            body: JSON.stringify({
                node_id: nodeId,
                sensor_type: "Semua Sensor",
                value: 0,
                status: "aman",
            }),
        })
            .then((res) => res.json())
            .then((res) =>
                console.log(`ℹ️ [${nodeId}] Status aman dikirim:`, res)
            )
            .catch((err) =>
                console.error(`❌ Gagal kirim status aman [${nodeId}]:`, err)
            );
    }
};
