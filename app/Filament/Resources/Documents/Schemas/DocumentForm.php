<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Nadu;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('nadu_id')
                    ->label('Nadu Number')
                    ->options(
                        Nadu::query()
                            ->pluck('nadu_ankaya', 'id')
                    )
                    ->searchable()
                    ->required(),
            ]);
    }
}