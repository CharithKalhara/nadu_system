<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->color('primary')
                    ->url(fn ($record) => route(
                        'filament.admin.pages.company-dashboard',
                        ['company' => $record->id]
                    )),

                Tables\Columns\TextColumn::make('table_name')
                    ->label('Table Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

            ])

            ->filters([
                //
            ])

            ->recordActions([

                EditAction::make(),

            ])

            ->toolbarActions([
                //
            ]);
    }
}
