<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\Action;
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
                    ->sortable(),

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
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-folder-open')
                    ->color('primary')
                    ->action(function ($record) {

                        session([
                            'company_name'  => $record->company_name,
                            'company_table' => $record->table_name,
                        ]);

                        return redirect()->route('filament.admin.resources.nadus.index');
                    }),

                EditAction::make(),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}