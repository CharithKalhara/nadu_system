<?php

namespace App\Models;

use App\Support\TypingCompanyContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TypingNadu extends Model
{
    protected $connection = 'companies';

    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope('typing_company_context', function (Builder $query): void {
            $companyId = session('typing_company_id');

            if (filter_var($companyId, FILTER_VALIDATE_INT) === false || (int) $companyId < 1) {
                $query->whereRaw('1 = 0');

                return;
            }

            $query->where($query->getModel()->qualifyColumn('company_id'), (int) $companyId);
        });

        static::creating(function (self $nadu): void {
            $company = app(TypingCompanyContext::class)->resolve(request());
            $companyId = (int) $company->getKey();

            abort_if($nadu->company_id !== null && (int) $nadu->company_id !== $companyId, 403);

            $nadu->company_id = $companyId;
        });

        static::updating(function (self $nadu): void {
            $companyId = (int) session('typing_company_id');

            abort_if($companyId < 1, 404);
            abort_if((int) $nadu->getOriginal('company_id') !== $companyId, 403);
            abort_if((int) $nadu->company_id !== $companyId, 403);
        });
    }

    public function getTable(): string
    {
        return app(TypingCompanyContext::class)->resolve(request())->table_name;
    }
}
