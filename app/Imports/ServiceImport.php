<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ServiceImport implements ToModel, WithChunkReading, WithBatchInserts, WithHeadingRow
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $name     = $row['nom'] ?? $row[1] ?? null;
        $customer = Customer::where('name', 'like', $name . '%')->first();

        if (Carbon::parse($row['fecha_lleg']) > Carbon::parse('2022-01-01')) {
            return new Service([
                'id'             => $row['contrato'] ?? $row[0],
                'customer_id'    => $customer ? $customer->id : 0,
                'admin_id'       => $row['g_id_usua'] ?? $row[2],
                'tech_id'        => $row['t_id_usua'] ?? $row[3],
                'equipo'         => $row['tpc'] ?? $row[4] ?? '',
                'marca'          => $row['m'] ?? $row[5] ?? null,
                'accesorios'     => $row['accesorios'] ?? $row[6] ?? null,
                'falla'          => $row['falla_apa'] ?? $row[7] ?? null,
                'notas'          => $row['otro'] ?? $row[8] ?? null,
                'monto_estimado' => 0,
                'date_repair'    => $row['fecha_repa'] != '0000-00-00' ? Carbon::parse($row['fecha_repa']) : $row[10] ?? null,
                'date_delivery'  => $row['fecha_entre'] != '0000-00-00' ? Carbon::parse($row['fecha_entre']) : $row[11] ?? null,
                'delivery'       => $row['entregado'] ?? $row[13] ?? null,
                'created_at'     => $row['fecha_lleg'] != '0000-00-00' ? Carbon::parse($row['fecha_lleg']) : $row[9] ?? null,
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
