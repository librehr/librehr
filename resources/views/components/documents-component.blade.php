@props([
    'documentsAll'
])
@empty(!$documentsAll)
    <div class="grid grid-cols-2 gap-6" >
        @foreach ($documentsAll as $category => $documents)
            <x-filament::modal id="document-{{$category}}" width="xl" :close-by-clicking-away="true" slide-over>

                <x-slot name="trigger">
                    <div class="flex justify-between w-full border rounded-lg bg-white px-6 py-3 items-center cursor-pointer hover:bg-gray-50">
                        {{ $category }} <span class="bg-gray-100 p-2 text-xs rounded">{{ count($documents) }}</span>
                    </div>
                </x-slot>

                <span class="font-bold text-2xl">{{ $category }}</span>
                @foreach($documents as $document)
                    <div class="flex flex-col  hover:bg-gray-100 p-2 cursor-pointer border  ">
                        <div class="flex flex-row justify-between items-start">
                            <a href="{{ route('download-document', [$document['uuid']]) }}" target="_blank" class="text-primary-600">{{ $document['name'] }}</a>
                            <span> {{ \Illuminate\Support\Number::fileSize($document['size']) }}</span>
                        </div>
                        <span class="text-sm">Uploaded by <span class="font-semibold">{{ data_get($document, 'uploadedBy.name')  }}</span> {{ \Carbon\Carbon::parse(data_get($document, 'created_at')) }}</span>
                    </div>
                @endforeach


                <x-slot name="footerActions">
                    {{-- Modal footer actions --}}
                </x-slot>
            </x-filament::modal>
        @endforeach
    </div>
@else
    <x-filament::section class="text-sm">
        No documents uploaded.
    </x-filament::section>
@endempty
