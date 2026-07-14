<x-filament-panels::page>

    <div class="space-y-8">

        {{-- Welcome --}}
        <x-filament::section>

            <h1 class="text-3xl font-bold">
                {{ session('company_name') }}
            </h1>

            <p class="mt-2 text-gray-600">
                Debt Recovery Management System
            </p>

        </x-filament::section>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <x-filament::card>
                <div class="text-center">
                    <div class="text-4xl font-bold text-primary-600">
                        {{ number_format($this->caseCount) }}
                    </div>

                    <div class="mt-2 text-gray-500">
                        Total Cases
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-4xl font-bold text-success-600">
                        {{ number_format($this->documentCount) }}
                    </div>

                    <div class="mt-2 text-gray-500">
                        Documents
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-4xl font-bold text-warning-600">
                        {{ number_format($this->totalAmount, 2) }}
                    </div>

                    <div class="mt-2 text-gray-500">
                        Total Debt
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-4xl font-bold text-danger-600">
                        {{ number_format($this->openCases) }}
                    </div>

                    <div class="mt-2 text-gray-500">
                        Open Cases
                    </div>
                </div>
            </x-filament::card>

        </div>

        {{-- Main Menu --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            <x-filament::card>
                <div class="flex flex-col items-center justify-between text-center h-72">

                    <div>

                        <div class="text-7xl">📁</div>

                        <h2 class="mt-4 text-2xl font-bold">
                            Nadu Cases
                        </h2>

                        <p class="mt-2 text-gray-500">
                            View, create, edit and manage all court cases.
                        </p>

                    </div>

                    <x-filament::button
                        tag="a"
                        :href="route('filament.admin.resources.nadus.index')">
                        Open Cases
                    </x-filament::button>

                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex flex-col items-center justify-between text-center h-72">

                    <div>

                        <div class="text-7xl">📄</div>

                        <h2 class="mt-4 text-2xl font-bold">
                            Documents
                        </h2>

                        <p class="mt-2 text-gray-500">
                            Generate and manage legal documents.
                        </p>

                    </div>

                    <x-filament::button
                        color="success"
                        tag="a"
                        :href="route('filament.admin.resources.documents.index')">
                        Open Documents
                    </x-filament::button>

                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex flex-col items-center justify-between text-center h-72">

                    <div>

                        <div class="text-7xl">🏢</div>

                        <h2 class="mt-4 text-2xl font-bold">
                            Switch Company
                        </h2>

                        <p class="mt-2 text-gray-500">
                            Return to the company list.
                        </p>

                    </div>

                    <x-filament::button
                        color="gray"
                        tag="a"
                        :href="route('filament.admin.resources.companies.index')">
                        Companies
                    </x-filament::button>

                </div>
            </x-filament::card>

        </div>

    </div>

</x-filament-panels::page>