<?php

namespace App\Imports;

use App\Models\PieceService;
use App\Models\Service;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PieceServiceImport implements ToModel, WithChunkReading, WithBatchInserts, WithHeadingRow
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if ($service = Service::find($row['contrato'])) {
            return new PieceService([
                'service_id' => (int) ($row['contrato'] ?? null),
                'quantity'   => $row['cant'] ?? null,
                'piece'      => $row['e_p'] ?? null,
                'serial'     => $row['serial'] ?? null,
                'price'      => $row['precio'] ?? null,
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