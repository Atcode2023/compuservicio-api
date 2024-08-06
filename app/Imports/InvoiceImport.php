<?php

namespace App\Imports;

use App\Models\Invoice;
use App\Models\Service;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InvoiceImport implements ToModel, WithChunkReading, WithBatchInserts, WithHeadingRow
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if ($service = Service::find($row['contrato'])) {
            return new Invoice([
                'service_id' => $row['contrato'] ?? null,
                'tech_id'    => $row['t_id_usua'] ?? null,
                'total'      => $row['monto'] ?? null,
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