<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\DeleteAction;
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
                    ->url(function ($record) {

                        session([
                            'company_id' => $record->id,
                            'company_name' => $record->company_name,
                            'company_table' => $record->table_name,
                        ]);

                        return route('filament.admin.resources.nadus.index');
                    }),

                Tables\Columns\TextColumn::make('table_name')
                    ->label('Table Name')
                    ->searchable(),

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
                DeleteAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}