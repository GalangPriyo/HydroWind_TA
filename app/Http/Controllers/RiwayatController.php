<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Device;
use App\Models\Sensor;
use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RiwayatController extends Controller
{
    /**
     * Tampilkan halaman riwayat menggunakan Inertia
     */
    public function indexRiwayat(Request $request)
    {
        // Ambil parameter filter dari request
        $deviceId = $request->input('device_id');
        $sensorType = $request->input('sensor_type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $perPage = $request->input('per_page', 200);

        // Daftar semua perangkat untuk filter
        $devices = Device::select('id', 'name', 'node_id', 'location')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Query untuk data sensor
        $query = SensorData::select(
            'sensor_datas.*',
            'sensors.name as sensor_name',
            'devices.name as device_name',
            'devices.node_id'
        )
            ->join('sensors', 'sensors.id', '=', 'sensor_datas.sensor_id')
            ->join('devices', 'devices.id', '=', 'sensors.device_id');

        // Filter berdasarkan perangkat
        if ($deviceId) {
            $query->where('devices.id', $deviceId);
        }

        // Filter berdasarkan jenis sensor
        if ($sensorType) {
            $query->where('sensors.name', $sensorType);
        }

        // Filter berdasarkan rentang tanggal
        if ($dateFrom) {
            $query->whereDate('sensor_datas.timestamp', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('sensor_datas.timestamp', '<=', $dateTo);
        }

        // Urutkan berdasarkan waktu terbaru
        $query->orderBy('sensor_datas.timestamp', 'desc');

        // Paginate hasil
        $sensorData = $query->paginate($perPage);
        $sensorData->getCollection()->transform(function ($item) {
            $item->timestamp = \Carbon\Carbon::parse($item->timestamp)
                ->timezone('Asia/Jakarta') // konversi ke WIB
                ->format('Y-m-d H:i:s');
            return $item;
        });

        // Query untuk statistik
        $statsQuery = SensorData::select(
            'sensors.name as sensor_name',
            DB::raw('MIN(sensor_datas.value) as min_value'),
            DB::raw('MAX(sensor_datas.value) as max_value'),
            DB::raw('AVG(sensor_datas.value) as avg_value'),
            DB::raw('COUNT(sensor_datas.id) as data_count')
        )
            ->join('sensors', 'sensors.id', '=', 'sensor_datas.sensor_id')
            ->join('devices', 'devices.id', '=', 'sensors.device_id')
            ->groupBy('sensors.name');

        // Terapkan filter yang sama ke statistik
        if ($deviceId) {
            $statsQuery->where('devices.id', $deviceId);
        }

        if ($sensorType) {
            $statsQuery->where('sensors.name', $sensorType);
        }

        if ($dateFrom) {
            $statsQuery->whereDate('sensor_datas.timestamp', '>=', $dateFrom);
        }

        if ($dateTo) {
            $statsQuery->whereDate('sensor_datas.timestamp', '<=', $dateTo);
        }

        $stats = $statsQuery->get();

        // Kirim data ke Vue component melalui Inertia
        return Inertia::render(auth()->user()->role === 'admin' ? 'Admin/AdminRiwayat' : 'User/UserRiwayat', [
            'user' => auth()->user(),
            'devices' => $devices,
            'sensorData' => $sensorData,
            'stats' => $stats,
            'filters' => [
                'device_id' => $deviceId,
                'sensor_type' => $sensorType,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'per_page' => $perPage
            ]
        ]);
    }

    /**
     * Download sensor data as Excel file
     */
    public function downloadRiwayat(Request $request)
    {
        // Ambil parameter filter dari request
        $deviceId = $request->input('device_id');
        $sensorType = $request->input('sensor_type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Query untuk data sensor
        $query = SensorData::select(
            'sensor_datas.timestamp',
            'sensor_datas.value',
            'sensors.name as sensor_name',
            'devices.name as device_name',
            'devices.node_id'
        )
            ->join('sensors', 'sensors.id', '=', 'sensor_datas.sensor_id')
            ->join('devices', 'devices.id', '=', 'sensors.device_id');

        if ($deviceId) {
            $query->where('devices.id', $deviceId);
        }

        if ($sensorType) {
            $query->where('sensors.name', $sensorType);
        }

        if ($dateFrom) {
            $query->whereDate('sensor_datas.timestamp', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('sensor_datas.timestamp', '<=', $dateTo);
        }

        $query->orderBy('sensor_datas.timestamp', 'desc');

        $sensorData = $query->get();

        // Group data berdasarkan node_id
        $groupedByDevice = $sensorData->groupBy('node_id');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Hapus sheet default

        foreach ($groupedByDevice as $nodeId => $dataGroup) {
            $sensorTypes = $dataGroup->pluck('sensor_name')->unique()->values();

            // Kelompokkan berdasarkan timestamp
            $groupedData = [];
            foreach ($dataGroup as $data) {
                $timestamp = date('Y-m-d H:i:s', strtotime($data->timestamp));
                if (!isset($groupedData[$timestamp])) {
                    $groupedData[$timestamp] = [
                        'timestamp' => $timestamp,
                        'sensors' => []
                    ];
                }
                $groupedData[$timestamp]['sensors'][$data->sensor_name] = $data->value;
            }

            krsort($groupedData); // Urutkan berdasarkan waktu terbaru

            // Buat sheet baru
            $sheetTitle = $nodeId;
            $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $sheetTitle);
            $spreadsheet->addSheet($sheet);

            // Header
            $sheet->setCellValue('A1', 'Waktu');
            $column = 'B';
            foreach ($sensorTypes as $type) {
                $sheet->setCellValue($column . '1', $this->formatSensorName($type));
                $column++;
            }

            // Style header
            $sheet->getStyle('A1:' . $column . '1')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => ['argb' => 'FFE0E0E0'],
                    'endColor' => ['argb' => 'FFFFFFFF'],
                ],
            ]);

            // Isi data
            $row = 2;
            foreach ($groupedData as $data) {
                $dateTime = new \DateTime($data['timestamp']);
                $formattedDate = $dateTime->format('d M Y H:i:s');
                $sheet->setCellValue('A' . $row, $formattedDate);

                $column = 'B';
                foreach ($sensorTypes as $type) {
                    $value = $data['sensors'][$type] ?? '-';
                    $unit = $this->getSensorUnit($type);
                    $sheet->setCellValue($column . $row, $value . ($value !== '-' ? ' ' . $unit : ''));
                    $column++;
                }

                $row++;
            }

            // Auto size
            foreach (range('A', $column) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Filter dan freeze header
            $sheet->setAutoFilter('A1:' . $column . '1');
            $sheet->freezePane('A2');
        }

        // Simpan file
        $fileName = 'Riwayat_Sensor';

        if ($deviceId) {
            $fileName .= '_' . $nodeId;
        }
        if ($sensorType) $fileName .= '_' . str_replace(' ', '_', strtolower($sensorType));
        if ($dateFrom && $dateTo) {
            $fileName .= '_' . date('Ymd', strtotime($dateFrom)) . '-' . date('Ymd', strtotime($dateTo));
        }

        $fileName .= '.xlsx';


        $temp_file = tempnam(sys_get_temp_dir(), 'sensor_data');
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }


    /**
     * Format nama sensor untuk tampilan
     */
    private function formatSensorName($sensorType)
    {
        // Ubah format nama sensor sesuai kebutuhan
        $formats = [

            // Tambahkan format lain sesuai kebutuhan
            'curah_hujan' => "Curah Hujan",
            'ketinggian_air' => "Ketinggian Air",
            'kecepatan_angin' => "Kecepatan Angin",
            'tekanan_udara' => "Tekanan Udara",
        ];

        return $formats[$sensorType] ?? ucfirst(str_replace('_', ' ', $sensorType));
    }

    /**
     * Dapatkan unit untuk jenis sensor
     */
    private function getSensorUnit($sensorType)
    {
        // Tetapkan unit untuk setiap jenis sensor
        $units = [
            'curah_hujan' => "mm",
            'ketinggian_air' => "cm",
            'kecepatan_angin' => "km/jam",
            'tekanan_udara' => "hPa",
            // Tambahkan unit lain sesuai kebutuhan
        ];

        return $units[$sensorType] ?? '';
    }

    public function truncateRiwayat()
    {
        DB::table('sensor_datas')->truncate();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
