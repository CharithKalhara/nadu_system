<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Models\Nadu;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('document_type')
                    ->label('Document Type')
                    ->options([
                        'sithasi' => 'Sithasi',
                        'envelope' => 'Envelope',
                        'sithasi_and_envelope' => 'Sithasi and Envelope',
                    ])
                    ->default('sithasi')
                    ->required(),

                Select::make('nadu_id')
                    ->label('Nadu Number')
                    ->options(fn (): array => self::getNaduOptions())
                    ->searchable()
                    ->required(),
            ]);
    }

    private static function getNaduOptions(): array
    {
        if (! session('company_id')) {
            return [];
        }

        return Nadu::query()
            ->where('company_id', session('company_id'))
            ->orderBy('nadu_ankaya')
            ->pluck('nadu_ankaya', 'id')
            ->all();
    }
}
