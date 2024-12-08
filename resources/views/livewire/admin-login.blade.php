<div>
    @if($admin)
        @if($adminPanel)
            <a href="{{ route('filament.app.pages.dashboard', \App\Models\Business::query()->first()->id) }}">App</a>
        @else
            <a href="{{ route('filament.admin.pages.dashboard') }}">Admin</a>
        @endif
    @endif
</div>
