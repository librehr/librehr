<ul class="p-6 border-b flex flex-row justify-center items-center gap-8">

    <a href="{{ route($route,$previous) }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
        </svg>
    </a>
    <div class="min-w-[160px] text-center">
        {{ $selected }}
    </div>
    @if ($next !== null)
    <a href="{{ route($route,$next) }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
        </svg>
    </a>
    @else
        <span class="w-6 h-6"></span>
    @endif
</ul>
