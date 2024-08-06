<?php

namespace App\Imports;

use App\Models\ReportService;
use App\Models\Service;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ReportServiceImport implements ToModel, WithChunkReading, WithBatchInserts, WithHeadingRow
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if ($service = Service::find($row['contrato'])) {
            return new ReportService([
                'service_id' => (int) ($row['contrato'] ?? null),
                'report'     => $row['repor_tecni'] ?? null,
                'price'      => $row['monto'] ?? null,
            ]);
        }
    }

    public function headingRow() : int
    {
        return 1;
    }

    public function chunkSize() : int
    {
        return 1000;
    }

    public function batchSize() : int
    {
        return 1000;
    }
}