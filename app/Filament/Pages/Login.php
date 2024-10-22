<?php

namespace App\Filament\Pages;

use App\Filament\Resources\UserResource;
use App\Models\AbsenceType;
use App\Models\Contract;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;


class Login extends \Filament\Pages\Auth\Login {
    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => 'demo@librehr.com',
            'password' => '12345678',
            'remember' => true,
        ]);
    }
}
