<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentUpdate extends Model
{
    protected $fillable = [
    'user_id', 'patient_name', 'prescription_name', 'status',
    'shipment_id', 'date_shipped', 'delivered_at', "rx_number"
    ];
}
