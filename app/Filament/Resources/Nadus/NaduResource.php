<?php

namespace App\Filament\Resources\Nadus;

use App\Support\CompanyContext;
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
use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $companyId = app(CompanyContext::class)->id();

        return $companyId
            ? $query->where('company_id', $companyId)
            : $query->whereRaw('1 = 0');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getRoutePrefix(Panel $panel): string
    {
        return 'companies/{company}/nadus';
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        $parameters['company'] ??= app(CompanyContext::class)->id();

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
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
