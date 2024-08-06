<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Traits\RecordsChanges;

class Customer extends Model
{
    use HasFactory, HasUuid, RecordsChanges;

    protected $fillable = [
        'name',
        'ci',
        'address',
        'phone',
    ];
}
