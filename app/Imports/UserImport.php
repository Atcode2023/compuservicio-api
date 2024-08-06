<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\User;

class UserImport implements ToModel, WithChunkReading, WithBatchInserts, WithHeadingRow
{
    /**
    *  @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'id' => $row['id_usua'] ?? $row[0],
            'name' => $row['usua'] ?? $row[1],
            'email' => strtolower($row['usua'] ?? $row[1]).'@cs.com',
            'role_id' => $row['nivel'] ?? $row[3],
            'ci' => $row['ci'] ?? $row[4],
            'password' => bcrypt(123456),
            'status' => 1,
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
