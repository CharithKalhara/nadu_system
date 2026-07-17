<?php

namespace App\Filament\Typing\Resources\Nadus;

use App\Filament\Typing\Resources\Nadus\Pages\CreateNadu;
use App\Filament\Typing\Resources\Nadus\Pages\EditNadu;
use App\Filament\Typing\Resources\Nadus\Pages\ListNadus;
use App\Filament\Typing\Resources\Nadus\Schemas\NaduForm;
use App\Filament\Typing\Resources\Nadus\Tables\NadusTable;
use App\Models\TypingNadu;
use App\Support\TypingCompanyContext;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NaduResource extends Resource
{
    protected static ?string $model = TypingNadu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nadu_ankaya';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return NaduForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NadusTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $company = app(TypingCompanyContext::class)->resolve(request());

        return parent::getEloquentQuery()->where('company_id', $company->getKey());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNadus::route('/'),
            'create' => CreateNadu::route('/create'),
            'edit' => EditNadu::route('/{record}/edit'),
        ];
    }
}
