<x-filament-panels::page>
    <div class="flex flex-col">
        <ul class="p-6 border-b border-t flex flex-row justify-center items-center gap-8">

            <button href="#" wire:click="change('previous')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                </svg>
            </button>
            <div class="min-w-[160px] text-center">
                {{ \Carbon\Carbon::parse($date)->format('F, Y') }}
            </div>
            @if (now()->subMonth() > \Carbon\Carbon::parse($date))
                <button wire:click="change('next')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                    </svg>
                </button>
            @else
                <span class="w-6 h-6"></span>
            @endif
        </ul>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
