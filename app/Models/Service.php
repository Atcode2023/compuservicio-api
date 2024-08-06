<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Traits\RecordsChanges;

class Service extends Model
{
    use HasFactory, HasUuid, RecordsChanges;

    protected $fillable = [
        'customer_id',
        'admin_id',
        'tech_id',
        'equipo',
        'marca',
        'accesorios',
        'falla',
        'notas',
        'monto_estimado',
        'date_repair',
        'date_delivery',
        'delivery',
        'abonos',
    ];

    protected $with = ['reports', 'pieces', 'customer'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    public function tech()
    {
        return $this->belongsTo(User::class, 'tech_id', 'id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function reports()
    {
        return $this->hasMany(ReportService::class);
    }
    public function pieces()
    {
        return $this->hasMany(PieceService::class);
    }
}
