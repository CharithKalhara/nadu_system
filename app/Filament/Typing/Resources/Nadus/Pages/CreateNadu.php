<?php

namespace App\Filament\Typing\Resources\Nadus\Pages;

use App\Filament\Typing\Resources\Nadus\NaduResource;
use App\Support\TypingCompanyContext;
use Filament\Resources\Pages\CreateRecord;

class CreateNadu extends CreateRecord
{
    protected static string $resource = NaduResource::class;

    public function mount(): void
    {
        app(TypingCompanyContext::class)->resolve(request());

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(TypingCompanyContext::class)->resolve(request())->getKey();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return NaduResource::getUrl('index', ['company' => request()->query('company')]);
    }
}
