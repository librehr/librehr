<div class="grid grid-cols-2">
    <div class="flex flex-col">
        <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
            {{ $this->getNavigationLabel() }}
        </h1>
        <p>
            {{ $this->getSubheading() }}
        </p>
    </div>
    <div class="flex flex-col border rounded-lg py-4 divide-y gap-4 items-center justify-center">
        <div class="w-full flex justify-center">
            Attendance
        </div>
        <div class="w-full flex flex-col gap-4 justify-center items-center min-h-[150px] ">
            <span>
                Today: $time
            </span>
            <x-filament::button color="primary" wire:click="openNewUserModal">
                Entrance
            </x-filament::button>
        </div>
    </div>
</div>


