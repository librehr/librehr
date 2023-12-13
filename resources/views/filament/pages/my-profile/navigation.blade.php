<div class="flex justify-center items-center p-4 gap-2 text-gray-500">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>

    {{ ucfirst(\Illuminate\Support\Facades\Auth::user()->name) }}
</div>
<ul class="p-4 border-b border-t flex flex-row justify-center items-center gap-8">
    @foreach ($links as $name=>$routeName)
    <a href="{{ route($routeName) }}" wire:navigate.hover class="{{ Route::currentRouteName() === $routeName ? 'border-b-2 border-black py-2' : '' }}">
        {{ $name }}
    </a>
    @endforeach
</ul>
