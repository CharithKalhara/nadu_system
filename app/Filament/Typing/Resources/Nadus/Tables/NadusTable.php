<?php

namespace App\Filament\Typing\Resources\Nadus\Tables;

use App\Filament\Typing\Resources\Nadus\NaduResource;
use App\Models\TypingNadu;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NadusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nadu_ankaya')->label('Case No')->searchable()->sortable(),
                TextColumn::make('nayakaru1_nama')->label('Debtor')->searchable()->sortable()->limit(40),
                TextColumn::make('nayakaru1_samajika_ankaya')->label('Member No')->searchable(),
                TextColumn::make('dun_naya_mudala')->label('Loan Amount')->money('LKR')->sortable(),
                TextColumn::make('arawul_mudala')->label('Dispute Amount')->money('LKR')->sortable(),
                TextColumn::make('dun_dinaya')->label('Loan Date')->date(),
                TextColumn::make('total')->label('Total')->money('LKR')->sortable(),
            ])
            ->recordActions([
                EditAction::make()->url(fn (TypingNadu $record): string => NaduResource::getUrl('edit', [
                    'record' => $record,
                    'company' => request()->query('company'),
                ])),
                DeleteAction::make(),
            ]);
    }
}
