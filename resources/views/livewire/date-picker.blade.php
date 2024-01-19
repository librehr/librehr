@props([
    'startDate',
    'endDate',

    ])
<div class="flex flex-col gap-4">
    <div class="flex flex-col">
        <label for="start_date" class="font-semibold">Start Date:</label>
        <input type="date"

               wire:model.live="startDate"
               id="start_date"
               class="border border-gray-300 rounded-lg mt-2"
        >
    </div>
    <div class="flex flex-col">
        <label for="end_date" class="font-semibold">End Date:</label>
        <input type="date"  wire:model.change="endDate" min="{{ $startDate }}" id="end_date"
               class="border border-gray-300 rounded-lg mt-2"
        >
    </div>

    @error('startDate') <span>{{ $message }}</span> @enderror
    @error('endDate') <span>{{ $message }}</span> @enderror
</div>
