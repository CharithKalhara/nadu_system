<?php

namespace App\Filament\Resources\Nadus\Tables;

use App\Services\RecipientDirectoryService;
use App\Services\SithasiService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

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

                        $document = app(SithasiService::class)->generate($record);

                        return response()->download(
                            storage_path('app/'.$document->file_path),
                            $document->file_name,
                        );
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('generateRecipientDirectory')
                        ->label('Generate Recipient Directory')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Generate Recipient Directory')
                        ->modalDescription('Creates one Word document containing the selected Nadu recipients.')
                        ->action(function (Collection $records) {
                            $directory = app(RecipientDirectoryService::class)
                                ->generateForNaduIds($records->modelKeys());

                            return response()->download(
                                $directory['path'],
                                $directory['fileName'],
                            );
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
