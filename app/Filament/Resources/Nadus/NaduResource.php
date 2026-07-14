<?php

namespace App\Filament\Resources\Nadus;

use App\Filament\Resources\Nadus\Pages\CreateNadu;
use App\Filament\Resources\Nadus\Pages\EditNadu;
use App\Filament\Resources\Nadus\Pages\ListNadus;
use App\Filament\Resources\Nadus\Pages\ViewNadu;
use App\Filament\Resources\Nadus\Schemas\NaduForm;
use App\Filament\Resources\Nadus\Schemas\NaduInfolist;
use App\Filament\Resources\Nadus\Tables\NadusTable;
use App\Models\Nadu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NaduResource extends Resource
{
    protected static ?string $model = Nadu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nadu_ankaya';

    // Hide from sidebar
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return NaduForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return NaduInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NadusTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNadus::route('/'),
            'create' => CreateNadu::route('/create'),
            'view' => ViewNadu::route('/{record}'),
            'edit' => EditNadu::route('/{record}/edit'),
        ];
    }
}