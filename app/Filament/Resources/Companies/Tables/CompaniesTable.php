<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema;

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
                    ))
                    ->openUrlInNewTab(),

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

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Company')
                    ->modalDescription('This will permanently delete the company and all of its case records.')
                    ->modalSubmitActionLabel('Delete')
                    ->before(function ($record) {

                        if (
                            Schema::connection('companies')
                                ->hasTable($record->table_name)
                        ) {
                            Schema::connection('companies')
                                ->drop($record->table_name);
                        }

                    }),

            ])

            ->toolbarActions([
                //
            ]);
    }
}
