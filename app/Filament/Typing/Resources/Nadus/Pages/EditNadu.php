<?php

namespace App\Filament\Typing\Resources\Nadus\Pages;

use App\Filament\Typing\Resources\Nadus\NaduResource;
use App\Support\TypingCompanyContext;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNadu extends EditRecord
{
    protected static string $resource = NaduResource::class;

    public function mount(int|string $record): void
    {
        app(TypingCompanyContext::class)->resolve(request());

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return NaduResource::getUrl('index', ['company' => request()->query('company')]);
    }
}
