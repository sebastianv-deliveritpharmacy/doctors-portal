<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentUpdate extends Model
{
    protected $fillable = [
        'user_id',
        'patient_name',
        'prescription_name',
        'status',
        'shipment_id',
        'rx_number',
        'date_shipped',
        'delivered_at',
        'date_of_birth',
        'insurance',
        'city',
        'arrived_to_office_date',
        'source',
    ];
}
