<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Traits\RecordsChanges;

class Toner extends Model
{
    use HasFactory, HasUuid, RecordsChanges;

    protected $fillable = [
        'customer_id',
        'admin_id',
        'model',
        'price',
        'date_delivery',
        'delivery',
        'abonos',
    ];

    protected $with = ['customer', 'pieces', 'toners'];
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

    public function toners()
    {
        return $this->hasMany(PieceToner::class);
    }

    public function pieces()
    {
        return $this->hasMany(ServiceToner::class);
    }
}
