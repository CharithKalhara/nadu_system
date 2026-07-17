<?php

namespace App\Filament\Typing\Resources\Nadus\Pages;

use App\Filament\Typing\Resources\Nadus\NaduResource;
use App\Support\TypingCompanyContext;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNadus extends ListRecords
{
    protected static string $resource = NaduResource::class;

    public function mount(): void
    {
        app(TypingCompanyContext::class)->resolve(request());

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->url(fn (): string => NaduResource::getUrl('create', [
                    'company' => request()->query('company'),
                ])),
        ];
    }
}
