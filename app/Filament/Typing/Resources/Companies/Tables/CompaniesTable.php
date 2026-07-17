<?php

namespace App\Filament\Typing\Resources\Companies\Tables;

use App\Filament\Typing\Resources\Nadus\NaduResource;
use App\Models\Company;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Company $record): string => NaduResource::getUrl('index', [
                'company' => $record->getKey(),
            ]))
            ->columns([
                TextColumn::make('company_name')
                    ->searchable(),
                TextColumn::make('table_name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
