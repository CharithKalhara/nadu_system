<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Models\Nadu;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
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
                        'statement' => 'Statement',
                        'cover_page' => 'Cover Page',
                        'thinduwa_yawima' => 'Thinduwa Yawima',
                        'thinduwa_written' => 'Thinduwa Written',
                        'wibaga_dinaya_1' => '1 Wibaga Dinaya',
                        'wibaga_dinaya_2' => '2 Wibaga Dinaya',
                        'mul_kola_2' => 'Mul Kola 2',
                        'hethupata' => 'Hethupata',
                        'sithasi_and_envelope' => 'Sithasi and Envelope',
                    ])
                    ->default('sithasi')
                    ->live()
                    ->required(),

                Radio::make('scope')
                    ->label('Generate For')
                    ->options([
                        'all' => 'All records in this company',
                        'selected' => 'Selected records',
                    ])
                    ->default('all')
                    ->live()
                    ->visible(fn (Get $get): bool => in_array($get('document_type'), ['sithasi', 'cover_page'], true))
                    ->required(fn (Get $get): bool => in_array($get('document_type'), ['sithasi', 'cover_page'], true)),

                Select::make('nadu_ids')
                    ->label('Select Records')
                    ->options(fn (): array => self::getNaduOptions())
                    ->multiple()
                    ->searchable()
                    ->visible(fn (Get $get): bool => in_array($get('document_type'), ['sithasi', 'cover_page'], true) && $get('scope') === 'selected')
                    ->required(fn (Get $get): bool => in_array($get('document_type'), ['sithasi', 'cover_page'], true) && $get('scope') === 'selected'),

                Select::make('nadu_id')
                    ->label('Nadu Number')
                    ->options(fn (): array => self::getNaduOptions())
                    ->searchable()
                    ->visible(fn (Get $get): bool => ! in_array($get('document_type'), ['sithasi', 'cover_page'], true))
                    ->required(fn (Get $get): bool => ! in_array($get('document_type'), ['sithasi', 'cover_page'], true)),
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
