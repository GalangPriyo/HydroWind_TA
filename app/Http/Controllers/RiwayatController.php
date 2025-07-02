<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Device;
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
        $validated = $request->validate([
            'node_id' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'device_id' => 'nullable|integer|exists:devices,id',
            'sensor_type' => 'nullable|string|in:curah_hujan,ketinggian_air,kecepatan_angin,tekanan_udara',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'per_page' => 'nullable|integer|min:1|max:500',
        ]);

        $devices = Device::query()
            ->select('id', 'name', 'node_id')
            ->orderBy('name')
            ->get();

        $query = SensorData::with(['sensor:id,name,device_id', 'sensor.device:id,name,node_id'])
            ->select('sensor_datas.id', 'sensor_datas.timestamp', 'sensor_datas.value', 'sensor_datas.sensor_id')
            ->join('sensors', 'sensors.id', '=', 'sensor_datas.sensor_id')
            ->join('devices', 'devices.id', '=', 'sensors.device_id');

        // Filter
        if ($validated['device_id'] ?? false) {
            $query->where('devices.id', $validated['device_id']);
        }

        if ($validated['sensor_type'] ?? false) {
            $query->where('sensors.name', $validated['sensor_type']);
        }

        if ($validated['date_from'] ?? false) {
            $query->where('sensor_datas.timestamp', '>=', $validated['date_from'] . ' 00:00:00');
        }

        if ($validated['date_to'] ?? false) {
            $query->where('sensor_datas.timestamp', '<=', $validated['date_to'] . ' 23:59:59');
        }

        $sensorData = $query->orderByDesc('sensor_datas.timestamp')
            ->paginate($validated['per_page'] ?? 200)
            ->through(function ($item) {
                return [
                    'id' => $item->id,
                    'timestamp' => Carbon::parse($item->timestamp)
                        ->timezone('Asia/Jakarta')
                        ->format('Y-m-d H:i:s'),
                    'value' => $item->value,
                    'sensor_name' => $item->sensor->name ?? null,
                    'device_name' => $item->sensor->device->name ?? null,
                    'node_id' => $item->sensor->device->node_id ?? null,
                ];
            });

        return Inertia::render(
            auth()->user()->role === 'admin' ? 'Admin/AdminRiwayat' : 'User/UserRiwayat',
            [
                'user' => ['role' => auth()->user()->role],
                'devices' => $devices,
                'sensorData' => $sensorData,
                'filters' => $validated,
                'meta' => [
                    'timezone' => 'Asia/Jakarta',
                    'max_per_page' => 500
                ]
            ]
        );
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
