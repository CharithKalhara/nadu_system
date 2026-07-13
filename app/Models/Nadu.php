<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nadu extends Model
{
    protected $connection = 'companies';

    protected $guarded = [];

    public function getTable()
    {
        return session('company_table', 'cases');
    }
}