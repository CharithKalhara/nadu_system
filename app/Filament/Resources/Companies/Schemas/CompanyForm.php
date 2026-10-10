<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company')
                    ->columns(2)
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255),
                        Select::make('status')
                            ->options([
                                'typing' => 'Typing',
                                'completed' => 'Completed',
                            ])
                            ->default('completed')
                            ->required(),
                    ]),

                Section::make('Sithasi details')
                    ->description('Defaults used when generating Sithasi documents for this company.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nadu_ankaya_format')
                            ->label('Nadu number format')
                            ->maxLength(255),
                        TextInput::make('teeraka')
                            ->label('Judge')
                            ->maxLength(255),
                        TextInput::make('karyalaya')
                            ->label('Court / office')
                            ->maxLength(255),
                        DatePicker::make('wibhaga_dinaya')
                            ->label('Hearing date'),
                        TimePicker::make('welawa')
                            ->label('Hearing time')
                            ->seconds(false),
                    ]),

                Section::make('Thinduwa details')
                    ->description('Dates and postage fee used in Thinduwa documents.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('teeraka_name_with_initials')
                            ->label('Judge name with initials')
                            ->maxLength(255),
                        DatePicker::make('thinduwa_labadena_dinaya')
                            ->label('Thinduwa issue date'),
                        DatePicker::make('gewia_yuthu_dinaya')
                            ->label('Payment due date'),
                        DatePicker::make('abiyachana_idiripath_kala_yuthu_dinaya')
                            ->label('Appeal submission date'),
                        TextInput::make('thepal_gasthu')
                            ->label('Postage fee')
                            ->numeric()
                            ->minValue(0),
                    ]),

                Section::make('Journal and representative details')
                    ->description('Society address, representative, and mailing addresses used in journal and Mul Kola 2 documents.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('samithiya_lipinaya')
                            ->label('Society address')
                            ->maxLength(255),
                        TextInput::make('niyojithaya')
                            ->label('Representative name')
                            ->maxLength(255),
                        TextInput::make('niyojithaya_lipinaya_1')
                            ->label('Representative address 1')
                            ->maxLength(255),
                        TextInput::make('niyojithaya_lipinaya_2')
                            ->label('Representative address 2')
                            ->maxLength(255),
                        TextInput::make('niyojithaya_lipinaya_3')
                            ->label('Representative address 3')
                            ->maxLength(255),
                    ]),

                Section::make('Sithasiya mailing details')
                    ->description('First and second Sithasiya dates, post offices, and receipt numbers.')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('first_sithasiya_date')
                            ->label('First Sithasiya date'),
                        TextInput::make('first_sithasiya_post_office')
                            ->label('First post office')
                            ->maxLength(255),
                        TextInput::make('first_sithasiya_receipt_no')
                            ->label('First receipt number')
                            ->maxLength(255),
                        DatePicker::make('second_sithasiya_date')
                            ->label('Second Sithasiya date'),
                        TextInput::make('second_sithasiya_post_office')
                            ->label('Second post office')
                            ->maxLength(255),
                        TextInput::make('second_sithasiya_receipt_no')
                            ->label('Second receipt number')
                            ->maxLength(255),
                    ]),

                Section::make('Examination details')
                    ->description('Defaults used in examination date documents.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('wibaga_sthanaya')
                            ->label('Examination location')
                            ->maxLength(255),
                    ]),
            ]);
    }
}
