@props([
    'documentsAll'
])
<div class="grid grid-cols-2 gap-6" >
    @foreach ($documentsAll as $category => $documents)
        <x-filament::modal :close-button="false" id="document-{{$category}}" width="xl" :close-by-clicking-away="false" slide-over>

            <x-slot name="trigger">
                <div class="flex justify-between w-full border rounded-lg bg-white px-4 py-2 items-center cursor-pointer hover:bg-gray-50">
                    {{ $category }} <span class="bg-gray-100 p-2 text-xs rounded">{{ count($documents) }}</span>
                </div>
            </x-slot>

            <span class="font-bold text-2xl">{{ $category }}</span>
            @foreach($documents as $document)
                <div class="flex flex-col  hover:bg-gray-100 p-2 cursor-pointer border  ">
                    <div class="flex flex-row justify-between items-start">
                        <span href="#" class="text-primary-600">{{ $document->name }}</span>
                        <span> {{ \Illuminate\Support\Number::fileSize($document->size) }}</span>
                    </div>
                    <span class="text-sm">Uploaded by <span class="font-semibold">{{ data_get($document, 'user.name')  }}</span> {{ $document->created_at->format('d/m/Y H:i') }}</span>
                </div>

            @endforeach
        </x-filament::modal>
    @endforeach
</div>
