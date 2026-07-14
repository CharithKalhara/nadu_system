<link rel="stylesheet" href="{{ asset('css/company-dashboard.css') }}">

<x-filament-panels::page>
    <div class="company-dashboard">
        <x-filament::section>
            <div class="company-dashboard__heading">
                <div>
                    <p class="company-dashboard__eyebrow">Company dashboard</p>
                    <h1 class="company-dashboard__title">{{ $this->company->company_name }}</h1>
                </div>

                <x-filament::button
                    tag="a"
                    color="gray"
                    icon="heroicon-o-building-office-2"
                    :href="route('filament.admin.resources.companies.index')"
                >
                    Switch company
                </x-filament::button>
            </div>
        </x-filament::section>

        <section class="quick-actions" aria-labelledby="quick-actions-heading">
            <div class="quick-actions__intro">
                <h2 id="quick-actions-heading">Quick Actions</h2>
                <p>Manage records and documents for {{ $this->company->company_name }}.</p>
            </div>

            <div class="quick-actions__grid">
                <a href="{{ $this->getNaduRecordsUrl() }}" class="quick-action-card quick-action-card--nadu">
                    <span class="quick-action-card__decoration" aria-hidden="true"></span>

                    <span class="quick-action-card__icon">
                        <x-filament::icon icon="heroicon-o-folder-open" />
                    </span>

                    <span class="quick-action-card__content">
                        <span class="quick-action-card__topline">
                            <span>
                                <span class="quick-action-card__title">Nadu Records</span>
                                <span class="quick-action-card__description">View all Nadu records for this company.</span>
                            </span>

                            <x-filament::icon icon="heroicon-o-arrow-right" class="quick-action-card__arrow" />
                        </span>

                        <span class="quick-action-card__count">{{ number_format($this->naduCount) }}</span>
                        <span class="quick-action-card__count-label">Total Nadu records</span>
                    </span>
                </a>

                <a href="{{ $this->getDocumentsUrl() }}" class="quick-action-card quick-action-card--documents">
                    <span class="quick-action-card__decoration" aria-hidden="true"></span>

                    <span class="quick-action-card__icon">
                        <x-filament::icon icon="heroicon-o-document-text" />
                    </span>

                    <span class="quick-action-card__content">
                        <span class="quick-action-card__topline">
                            <span>
                                <span class="quick-action-card__title">Documents</span>
                                <span class="quick-action-card__description">View all documents for this company.</span>
                            </span>

                            <x-filament::icon icon="heroicon-o-arrow-right" class="quick-action-card__arrow" />
                        </span>

                        <span class="quick-action-card__count">{{ number_format($this->documentCount) }}</span>
                        <span class="quick-action-card__count-label">Total documents</span>
                    </span>
                </a>
            </div>
        </section>
    </div>
</x-filament-panels::page>
