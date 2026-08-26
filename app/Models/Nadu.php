<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nadu extends Model
{
    protected $connection = 'companies';

    protected $guarded = [];

    protected $casts = [
        'dun_dinaya' => 'date',
        'awasan_mudal_bendima' => 'date',
        'apal_pa' => 'decimal:2',
        'apal_vi' => 'decimal:2',
    ];

    public function getTable()
    {
        return session('company_table', 'cases');
    }
}
