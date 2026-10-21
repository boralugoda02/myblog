<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $fillable = [
        'host', 'port', 'username', 'password',
        'encryption', 'from_address', 'from_name', 'is_active',
    ];

    protected $casts = [
        'password'  => 'encrypted',
        'is_active' => 'boolean',
    ];

    protected $hidden = ['password'];
}