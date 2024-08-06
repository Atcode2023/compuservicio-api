<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Traits\RecordsChanges;

class ServiceToner extends Model
{
    use HasFactory, HasUuid, RecordsChanges;

    protected $fillable = [
        'toner_id',
        'quantity',
        'piece',
        'serial',
        'price',
    ];
}
