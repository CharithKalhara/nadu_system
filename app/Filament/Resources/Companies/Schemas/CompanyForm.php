<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ]);
    }
}
