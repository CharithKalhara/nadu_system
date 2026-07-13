<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'nadu_id',
        'document_type',
        'file_name',
        'file_path',
        'generated_by',
    ];

    public function nadu(): BelongsTo
    {
        return $this->belongsTo(Nadu::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}