<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

trait RecordsChanges
{
    protected static function bootRecordsChanges()
    {
        static::updated(function ($model) {
            $model->recordChange('update');
        });

        static::created(function ($model) {
            $model->recordChange('insert');
        });

        static::deleted(function ($model) {
            if (in_array(SoftDeletes::class, class_uses($model))) {
                $model->recordChange('soft_delete');
            } else {
                $model->recordChange('delete');
            }
        });

        // static::restored(function ($model) {
        //     $model->recordChange('restore');
        // });
    }

    protected function recordChange($operation)
    {
        DB::table('sync_logs')->insert([
            'table_name' => $this->getTable(),
            'uuid' => $this->uuid,
            'operation' => $operation,
            'changed_at' => now(),
        ]);
    }
}
