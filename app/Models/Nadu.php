<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Support\CompanyContext;

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

    protected static function booted(): void
    {
        static::addGlobalScope('company_context', function (Builder $query): void {
            $companyId = app(CompanyContext::class)->id();

            if ($companyId === null) {
                $query->whereRaw('1 = 0');

                return;
            }

            $query->where($query->getModel()->qualifyColumn('company_id'), $companyId);
        });

        static::creating(function (self $nadu): void {
            $companyId = app(CompanyContext::class)->id();

            abort_unless($companyId !== null, 404);
            abort_if($nadu->company_id !== null && (int) $nadu->company_id !== $companyId, 403);

            $nadu->company_id = $companyId;
        });

        static::updating(function (self $nadu): void {
            $companyId = app(CompanyContext::class)->id();

            abort_unless($companyId !== null, 404);
            abort_if((int) $nadu->getOriginal('company_id') !== $companyId, 403);
            abort_if((int) $nadu->company_id !== $companyId, 403);
        });
    }

    public function getTable()
    {
        return app(CompanyContext::class)->tableName();
    }
}
