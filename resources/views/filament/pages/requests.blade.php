<x-filament-panels::page>
    <button
            class="font-semibold"
            x-data="{}"
            x-on:click="$dispatch('open-modal', { id: 'database-notifications' })"
            type="button"
    >
        Notifications
    </button>
    @empty(!$requests)
        <div class="">No requests pending.</div>
    @else
        <div class="container mx-auto max-w-4xl flex flex-col border rounded-lg divide-y">
            @foreach($requests as $request)
                @if (data_get($request, 'request.name') == 'absences')
                    <x-request-absences :request="$request"/>
                @endif
            @endforeach
        </div>
    @endempty
</x-filament-panels::page>
