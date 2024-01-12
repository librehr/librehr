@props([
    'title',
    'description',
])
<div>
    <div class="flex flex-col gap-1">
        <h2 class="text-xl font-bold flex flex-row items-center gap-2">
            <icon>
                {{ $icon }}
            </icon>
            {{ $title }}
        </h2>
        <p>
            {{ $description }}
        </p>
    </div>

    <div class="mt-4">
        {{ $slot }}
    </div>
</div>
