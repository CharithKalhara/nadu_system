<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Models\Company;
use App\Models\Nadu;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
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

                TextInput::make('nadu_ankaya_format')
                    ->label('නඩු අංකය ආකෘතිය')
                    ->default(fn (): ?string => self::company()?->nadu_ankaya_format)
                    ->visible(fn (Get $get): bool => $get('document_type') === 'sithasi')
                    ->required(fn (Get $get): bool => $get('document_type') === 'sithasi'),

                TextInput::make('teeraka')
                    ->label('තීරක')
                    ->default(fn (): ?string => self::company()?->teeraka)
                    ->visible(fn (Get $get): bool => $get('document_type') === 'sithasi')
                    ->required(fn (Get $get): bool => $get('document_type') === 'sithasi'),

                TextInput::make('karyalaya')
                    ->label('කාර්යාලය')
                    ->default(fn (): ?string => self::company()?->karyalaya)
                    ->visible(fn (Get $get): bool => $get('document_type') === 'sithasi')
                    ->required(fn (Get $get): bool => $get('document_type') === 'sithasi'),

                DatePicker::make('wibhaga_dinaya')
                    ->label('විභාග දිනය')
                    ->locale('si')
                    ->extraInputAttributes([
                        'x-on:focus' => "setTimeout(() => { const months = { Jan: 'ජනවාරි', January: 'ජනවාරි', Feb: 'පෙබරවාරි', February: 'පෙබරවාරි', Mar: 'මාර්තු', March: 'මාර්තු', Apr: 'අප්‍රේල්', April: 'අප්‍රේල්', May: 'මැයි', Jun: 'ජූනි', June: 'ජූනි', Jul: 'ජූලි', July: 'ජූලි', Aug: 'අගෝස්තු', August: 'අගෝස්තු', Sep: 'සැප්තැම්බර්', September: 'සැප්තැම්බර්', Oct: 'ඔක්තෝබර්', October: 'ඔක්තෝබර්', Nov: 'නොවැම්බර්', November: 'නොවැම්බර්', Dec: 'දෙසැම්බර්', December: 'දෙසැම්බර්' }; document.querySelectorAll('.flatpickr-monthDropdown-months option, .flatpickr-current-month .cur-month').forEach((month) => { if (months[month.textContent.trim()]) month.textContent = months[month.textContent.trim()]; }); }, 50)",
                        'x-on:click' => "setTimeout(() => { const months = { Jan: 'ජනවාරි', January: 'ජනවාරි', Feb: 'පෙබරවාරි', February: 'පෙබරවාරි', Mar: 'මාර්තු', March: 'මාර්තු', Apr: 'අප්‍රේල්', April: 'අප්‍රේල්', May: 'මැයි', Jun: 'ජූනි', June: 'ජූනි', Jul: 'ජූලි', July: 'ජූලි', Aug: 'අගෝස්තු', August: 'අගෝස්තු', Sep: 'සැප්තැම්බර්', September: 'සැප්තැම්බර්', Oct: 'ඔක්තෝබර්', October: 'ඔක්තෝබර්', Nov: 'නොවැම්බර්', November: 'නොවැම්බර්', Dec: 'දෙසැම්බර්', December: 'දෙසැම්බර්' }; document.querySelectorAll('.flatpickr-monthDropdown-months option, .flatpickr-current-month .cur-month').forEach((month) => { if (months[month.textContent.trim()]) month.textContent = months[month.textContent.trim()]; }); }, 50)",
                    ])
                    ->default(fn (): ?string => self::company()?->wibhaga_dinaya)
                    ->visible(fn (Get $get): bool => $get('document_type') === 'sithasi')
                    ->required(fn (Get $get): bool => $get('document_type') === 'sithasi'),

                TimePicker::make('welawa')
                    ->label('වේලාව (24 පැය)')
                    ->seconds(false)
                    ->native(false)
                    ->format('H:i')
                    ->default(fn (): ?string => self::company()?->welawa)
                    ->visible(fn (Get $get): bool => $get('document_type') === 'sithasi')
                    ->required(fn (Get $get): bool => $get('document_type') === 'sithasi'),

                Radio::make('scope')
                    ->label('Generate For')
                    ->options([
                        'all' => 'All records in this company',
                        'selected' => 'Selected records',
                    ])
                    ->default('all')
                    ->live()
                    ->visible(fn (Get $get): bool => in_array($get('document_type'), ['sithasi', 'cover_page', 'envelope'], true))
                    ->required(fn (Get $get): bool => in_array($get('document_type'), ['sithasi', 'cover_page', 'envelope'], true)),

                Select::make('nadu_ids')
                    ->label('Select Records')
                    ->options(fn (): array => self::getNaduOptions())
                    ->multiple()
                    ->searchable()
                    ->visible(fn (Get $get): bool => in_array($get('document_type'), ['sithasi', 'cover_page', 'envelope'], true) && $get('scope') === 'selected')
                    ->required(fn (Get $get): bool => in_array($get('document_type'), ['sithasi', 'cover_page', 'envelope'], true) && $get('scope') === 'selected'),

                Select::make('nadu_id')
                    ->label('Nadu Number')
                    ->options(fn (): array => self::getNaduOptions())
                    ->searchable()
                    ->visible(fn (Get $get): bool => ! in_array($get('document_type'), ['sithasi', 'cover_page', 'envelope'], true))
                    ->required(fn (Get $get): bool => ! in_array($get('document_type'), ['sithasi', 'cover_page', 'envelope'], true)),
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

    private static function company(): ?Company
    {
        return session('company_id') ? Company::find(session('company_id')) : null;
    }
}
