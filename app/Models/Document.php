<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\CompanyContext;

class Document extends Model
{
    protected $fillable = [
        'company_id',
        'nadu_id',
        'document_type',
        'file_name',
        'file_path',
        'generated_by',
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

        static::creating(function (self $document): void {
            $companyId = app(CompanyContext::class)->id();

            abort_unless($companyId !== null, 404);
            abort_if($document->company_id !== null && (int) $document->company_id !== $companyId, 403);

            $document->company_id = $companyId;

            abort_unless(
                $document->nadu_id !== null
                    && Nadu::query()->whereKey($document->nadu_id)->exists(),
                404,
            );
        });

        static::updating(function (self $document): void {
            $companyId = app(CompanyContext::class)->id();

            abort_unless($companyId !== null, 404);
            abort_if((int) $document->getOriginal('company_id') !== $companyId, 403);
            abort_if((int) $document->company_id !== $companyId, 403);

            if ($document->isDirty('nadu_id')) {
                abort_unless(
                    Nadu::query()->whereKey($document->nadu_id)->exists(),
                    404,
                );
            }
        });
    }

    public function nadu(): BelongsTo
    {
        return $this->belongsTo(Nadu::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
