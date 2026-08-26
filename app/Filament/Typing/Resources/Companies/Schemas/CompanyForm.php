<?php

namespace App\Filament\Typing\Resources\Companies\Schemas;

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
            ]);
    }
}
