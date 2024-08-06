<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUuidAndLastSyncedAtToAllTables extends Migration
{
    public function up()
    {
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];

            // Agregar columnas sin valores por defecto
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'uuid')) {
                    $table->uuid('uuid')->unique()->nullable();
                }
                if (!Schema::hasColumn($tableName, 'last_synced_at')) {
                    $table->timestamp('last_synced_at')->nullable();
                }
            });

            // Actualizar las filas existentes para establecer el valor de `uuid`
            DB::table($tableName)->update(['uuid' => DB::raw('UUID()')]);

            // Establecer la columna `uuid` como `NOT NULL`
            Schema::table($tableName, function (Blueprint $table) {
                $table->uuid('uuid')->nullable(false)->change();
            });
        }
    }

    public function down()
    {
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['uuid', 'last_synced_at']);
            });
        }
    }
}
