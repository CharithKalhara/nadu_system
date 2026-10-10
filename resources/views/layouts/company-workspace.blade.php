<x-filament-panels::layout.base :livewire="$livewire">
    <link rel="stylesheet" href="{{ asset('css/company-dashboard.css') }}">

    <header class="company-workspace-topbar">
        <a href="{{ route('filament.admin.pages.company-dashboard', ['company' => session('company_id')]) }}" class="company-workspace-topbar__brand">
            Nadu System
        </a>

        <a href="{{ url('/admin') }}" class="company-workspace-topbar__return">
            <x-filament::icon icon="heroicon-o-arrow-left" />
            Return to dashboard
        </a>
    </header>

    <main class="company-workspace-main">
        {{ $slot }}
    </main>
</x-filament-panels::layout.base>
