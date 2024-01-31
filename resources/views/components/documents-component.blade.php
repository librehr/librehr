@props([
    'documentsAll'
])
<div class="grid grid-cols-2 gap-8" >
    @foreach ($documentsAll as $category => $documents)
        <x-filament::modal id="document-{{$category}}" width="2xl">
            <x-slot name="trigger">
                <div class="flex justify-between w-full border rounded-lg bg-white px-4 py-2 items-center cursor-pointer hover:bg-gray-50">
                    {{ $category }} <span class="bg-gray-100 p-2 text-xs rounded">{{ count($documents) }}</span>
                </div>
            </x-slot>

            <span class="font-bold text-2xl">{{ $category }}</span>
            @foreach($documents as $document)
                <div class="flex flex-row justify-between items-start">
                    <a href="#" class="text-primary-600 hover:underline">{{ $document->name }}</a>
                    <span> {{ \Illuminate\Support\Number::fileSize($document->size) }}</span>
                </div>
            @endforeach
        </x-filament::modal>
    @endforeach
</div>
