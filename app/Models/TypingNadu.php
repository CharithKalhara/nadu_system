<?php

namespace App\Models;

use App\Support\TypingCompanyContext;
use Illuminate\Database\Eloquent\Model;

class TypingNadu extends Model
{
    protected $connection = 'companies';

    protected $guarded = [];

    public function getTable(): string
    {
        return app(TypingCompanyContext::class)->resolve(request())->table_name;
    }
}
