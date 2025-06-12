<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class CaretendCredential extends Model
{
    protected $fillable = ['user_id', 'client_id', 'secret'];

    public function setSecretAttribute($value)
    {
        $this->attributes['secret'] = Crypt::encryptString($value);
    }

    public function getSecretAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
