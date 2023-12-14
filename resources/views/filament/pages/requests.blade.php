<x-filament-panels::page>
    <button
            class=""
            x-data="{}"
            x-on:click="$dispatch('open-modal', { id: 'database-notifications' })"
            type="button"
    >
        Notifications
    </button>
    <div class="container mx-auto max-w-4xl flex flex-col border rounded-lg divide-y gap-4">
        @foreach(\Illuminate\Support\Facades\Auth::user()->notifications as $notification)
        <div class="flex flex-row gap-4 justify-between p-4 px-6">
            <div class="flex gap-4">
                <div class="flex flex-col px-6 gap-2">
            <span class="font-semibold">
                {{ data_get($notification, 'data.subject') }}
            </span>
                    <span class="text-gray-700 text-sm">
                 {{ data_get($notification, 'created_at')->format('d/m/Y H:i') }}
            </span>
                    <span class="text-gray-700 text-sm">
                 {{ data_get($notification, 'data.body') }}
            </span>
                </div>
            </div>
            <div>
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</x-filament-panels::page>
