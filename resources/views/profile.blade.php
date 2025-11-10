<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-4 text-dark">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">

            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <livewire:profile.delete-user-form />
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
