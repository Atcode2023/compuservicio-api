<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class AddUuidAndLastSyncedAtToAllTables extends Migration
{
    public function up()
    {
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            if ($tableName === 'migrations') {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'uuid')) {
                    $uuid = Uuid::uuid4();
                    $table->uuid('uuid')->default($uuid->toString());
                }
                if (!Schema::hasColumn($tableName, 'last_synced_at')) {
                    $table->timestamp('last_synced_at')->nullable();
                }
            });
        }
    }

    public function down()
    {
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {

            $tableName = array_values((array)$table)[0];
            if ($tableName === 'migrations') {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['uuid', 'last_synced_at']);
            });
        }
    }
}
