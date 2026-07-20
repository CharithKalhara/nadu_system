<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'table_name',
        'status',
        'nadu_ankaya_format',
        'teeraka',
        'karyalaya',
        'wibhaga_dinaya',
        'welawa',
    ];
}
