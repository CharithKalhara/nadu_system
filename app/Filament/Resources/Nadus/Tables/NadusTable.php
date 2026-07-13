<?php

namespace App\Filament\Resources\Nadus\Tables;

use App\Services\SithasiService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NadusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nadu_ankaya')
                    ->label('Case No')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nayakaru1_nama')
                    ->label('Debtor')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('nayakaru1_samajika_ankaya')
                    ->label('Member No')
                    ->searchable(),

                TextColumn::make('dun_naya_mudala')
                    ->label('Loan Amount')
                    ->money('LKR')
                    ->sortable(),

                TextColumn::make('arawul_mudala')
                    ->label('Dispute Amount')
                    ->money('LKR')
                    ->sortable(),

                TextColumn::make('dun_dinaya')
                    ->label('Loan Date')
                    ->date(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('LKR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),

                EditAction::make(),

                Action::make('generateSithasi')
                    ->label('Generate Sithasi')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->action(function ($record) {

                        $path = app(SithasiService::class)->generate($record);

                        return response()->download($path);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}