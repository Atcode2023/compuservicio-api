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
            if ($tableName === 'migrations') {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'uuid')) {
                    $table->string('uuid')->unique()->nullable();
                }
                if (!Schema::hasColumn($tableName, 'last_synced_at')) {
                    $table->timestamp('last_synced_at')->nullable();
                }
            });

            // Create trigger to set UUID on new rows
            $trigger = "
                CREATE TRIGGER set_uuid_before_insert
                BEFORE INSERT ON `{$tableName}`
                FOR EACH ROW
                BEGIN
                    IF NEW.uuid IS NULL THEN
                        SET NEW.uuid = UUID();
                    END IF;
                END;
            ";

            DB::unprepared($trigger);
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

            $triggerName = "set_uuid_before_insert";
            $dropTrigger = "DROP TRIGGER IF EXISTS `{$triggerName}`";
            DB::unprepared($dropTrigger);

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['uuid', 'last_synced_at']);
            });
        }
    }
}
