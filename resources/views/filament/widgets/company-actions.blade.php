<x-filament-widgets::widget>

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

</x-filament-widgets::widget>