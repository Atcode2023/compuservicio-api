<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUuidAndLastSyncedAtToAllTables extends Migration
{
    public function up()
    {
        // Primero, agrega las columnas `uuid` y `last_synced_at` como `nullable`
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'uuid')) {
                    $table->uuid('uuid')->unique()->nullable();
                }
                if (!Schema::hasColumn($tableName, 'last_synced_at')) {
                    $table->timestamp('last_synced_at')->nullable();
                }
            });
        }

        // Luego, actualiza las filas existentes para establecer el valor de `uuid`
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            DB::table($tableName)->whereNull('uuid')->update(['uuid' => DB::raw('UUID()')]);
        }

        // Finalmente, establece la columna `uuid` como `NOT NULL`
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            Schema::table($tableName, function (Blueprint $table) {
                $table->uuid('uuid')->nullable(false)->change();
            });
        }
    }

    public function down()
    {
        // Elimina las columnas `uuid` y `last_synced_at`
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['uuid', 'last_synced_at']);
            });
        }
    }
}
