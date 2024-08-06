<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class SyncController extends Controller
{
    const BATCH_SIZE = 100;

    public function bidirectionalSync(Request $request)
    {
        $type = $request->input('type', 'subir');
        $isPrincipal = $request->input('is_principal', false);
        $serverUrl = $request->input('server_url', null);
        $tableName = $request->input('table_name', null);
        $lastSync = $request->input('last_sync', null);
        $data = $request->input('data', []);

        if ($type === 'subir' && !$serverUrl) {
            return response()->json(['status' => 'error', 'message' => 'Server URL is required for upload'], 400);
        }

        if ($type === 'guardar') {
            if ($tableName) {
                $this->guardarDatos($tableName, $data);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Table name is required for guardar'], 400);
            }
            return response()->json(['status' => 'success'], 200);
        }

        if ($type === 'download') {
            if ($tableName) {
                return $this->downloadData($request);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Table name is required for download'], 400);
            }
            return response()->json(['status' => 'success'], 200);
        }

        if ($type === 'descargar' && !$serverUrl) {
            return response()->json(['status' => 'error', 'message' => 'Server URL is required for download'], 400);
        }

        if ($tableName) {
            $lastSync = $lastSync ?: $this->getLastSyncDate($tableName);
            $this->syncTable($type, $isPrincipal, $tableName, $lastSync, $serverUrl);
        } else {
            $tablesToIgnore = ['migrations', 'failed_jobs', 'cache_locks', 'password_resets'];
            $tables = DB::select('SHOW TABLES');

            $tableNames = array_filter(array_map(function ($table) {
                return array_values((array)$table)[0];
            }, $tables), function ($tableName) use ($tablesToIgnore) {
                return !in_array($tableName, $tablesToIgnore);
            });

            foreach ($tableNames as $tableName) {
                $lastSync = $lastSync ?: $this->getLastSyncDate($tableName);
                $this->syncTable($type, $isPrincipal, $tableName, $lastSync, $serverUrl);
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    protected function syncTable($type, $isPrincipal, $tableName, $lastSync, $serverUrl)
    {
        if ($type === 'subir') {
            $totalLocalChanges = DB::table('sync_logs')
                ->where('table_name', $tableName)
                ->where('changed_at', '>', $lastSync)
                ->count();

            $batches = ceil($totalLocalChanges / self::BATCH_SIZE);

            for ($batch = 0; $batch < $batches; $batch++) {
                $localChanges = DB::table('sync_logs')
                    ->where('table_name', $tableName)
                    ->where('changed_at', '>', $lastSync)
                    ->offset($batch * self::BATCH_SIZE)
                    ->limit(self::BATCH_SIZE)
                    ->get()
                    ->toArray();

                $this->syncSubir($isPrincipal, $tableName, $lastSync, $localChanges, $serverUrl);
            }
        } else if ($type === 'descargar') {
            $this->syncDescargar($isPrincipal, $tableName, $lastSync, $serverUrl);
        }
    }

    protected function syncSubir($isPrincipal, $tableName, $lastSync, $localChanges, $serverUrl)
    {
        $localChangesArray = array_map(function ($change) use ($tableName) {
            $uuid = $change->uuid;
            $operation = $change->operation;

            $data = DB::table($tableName)
                ->where('uuid', $uuid)
                ->first();

            $dataArray = $data ? (array)$data : [];

            return array_merge(['uuid' => $uuid, 'operation' => $operation], $dataArray);
        }, $localChanges);

        $response = Http::timeout(120)
            ->post($serverUrl . '/api/sync/bidirectional', [
                'type' => 'guardar',
                'table_name' => $tableName,
                'data' => $localChangesArray,
            ]);

        if ($response->successful()) {
            $this->markAsSynced($tableName, $localChanges);
            // $this->guardarDatos($tableName, $localChangesArray);
        } else {
            Log::error("Error en la respuesta de sincronización: " . $response->body());
        }
    }

    protected function markAsSynced($tableName, $localChanges)
    {
        $uuids = array_map(function ($change) {
            return $change->uuid;
        }, $localChanges);

        DB::table('sync_logs')
            ->whereIn('uuid', $uuids)
            ->update(['is_synced' => true]);
    }

    protected function syncDescargar($isPrincipal, $tableName, $lastSync, $serverUrl)
    {
        $response = Http::timeout(120)
            ->post($serverUrl . '/api/sync/bidirectional', [
                'type' => 'download',
                'table_name' => $tableName,
                'last_sync' => $lastSync,
            ]);

        if ($response->successful()) {
            $remoteChanges = $response->json();
            $this->guardarDatos($tableName, $remoteChanges);
        } else {
            Log::error("Error en la respuesta de descarga: " . $response->body());
        }
    }

    protected function guardarDatos($tableName, $remoteChanges)
    {
        Log::info("info: " . $tableName);
        Log::info("info2: " . json_encode($remoteChanges));

        DB::transaction(function () use ($tableName, $remoteChanges) {
            foreach ($remoteChanges as $change) {
                $changeWithoutId = Arr::except((array)$change, ['id', 'operation']);

                if ($change['operation'] === 'delete') {
                    DB::table($tableName)->where('uuid', $change['uuid'])->delete();
                } elseif ($change['operation'] === 'soft_delete') {
                    DB::table($tableName)->where('uuid', $change['uuid'])->update(['deleted_at' => now()]);
                } elseif ($change['operation'] === 'restore') {
                    DB::table($tableName)->where('uuid', $change['uuid'])->update(['deleted_at' => null]);
                } else {
                    if (!isset($changeWithoutId['ci'])) {
                        throw new \Exception("El campo 'ci' es requerido para las operaciones de insert/update.");
                    }

                    $record = DB::table($tableName)->where('uuid', $change['uuid'])->first();

                    if ($record) {
                        DB::table($tableName)->where('uuid', $change['uuid'])->update($changeWithoutId);
                    } else {
                        DB::table($tableName)->insert($changeWithoutId);
                    }
                }

                DB::table('sync_logs')->insert([
                    'table_name' => $tableName,
                    'uuid' => $change['uuid'],
                    'operation' => $change['operation'],
                    'changed_at' => $change['updated_at'] ?? now(),
                ]);
            }

            $uuids = array_map(function ($change) {
                return $change['uuid'];
            }, $remoteChanges);

            DB::table($tableName)
                ->whereIn('uuid', $uuids)
                ->update(['is_synced' => true]);

            DB::table($tableName)->update(['last_synced_at' => now()]);
        });
    }

    public function downloadData(Request $request)
    {
        $tableName = $request->input('table_name');
        $lastSync = $request->input('last_sync', '1970-01-01 00:00:00');

        // Obtener todos los cambios desde sync_logs
        $changes = DB::table('sync_logs')
            ->where('table_name', $tableName)
            ->where('changed_at', '>', $lastSync)
            ->get();

        // Filtrar y mapear los cambios
        $data = $changes->map(function ($change) use ($tableName) {
            $operation = $change->operation;
            $uuid = $change->uuid;

            // Obtener los datos del registro solo si la operación no es delete
            if ($operation === 'delete') {
                return ['uuid' => $uuid, 'operation' => $operation, 'uuid_changed_at' => $change->changed_at];
            } elseif (
                $operation ===

                'soft_delete'
            ) {
                return ['uuid' => $uuid, 'operation' => $operation, 'deleted_at' => $change->changed_at];
            } elseif ($operation === 'restore') {
                $data = DB::table($tableName)
                    ->where('uuid', $uuid)
                    ->first();

                if ($data) {
                    $dataArray = (array) $data;
                    return array_merge(['uuid' => $uuid, 'operation' => $operation, 'deleted_at' => null], $dataArray);
                }
            } else {
                $data = DB::table($tableName)
                    ->where('uuid', $uuid)
                    ->first();

                if ($data) {
                    $dataArray = (array) $data;
                    return array_merge(['uuid' => $uuid, 'operation' => $operation], $dataArray);
                }
            }

            return null;
        });

        // Filtrar nulos para asegurar que solo se devuelvan arrays completos
        $filteredData = $data->filter(function ($item) {
            return !is_null($item);
        });

        return response()->json($filteredData->values());
    }

    protected function getLastSyncDate($tableName)
    {
        $lastSyncDate = DB::table($tableName)
            ->orderBy('last_synced_at', 'desc')
            ->value('last_synced_at');

        return $lastSyncDate ?: now()->subDay()->toDateTimeString();
    }
}
