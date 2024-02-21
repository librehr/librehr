<?php

namespace App\Filament\Pages\MyProfile;

use App\Models\Contract;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;


class ProfileContracts extends Page  implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.my-profile.profile-contracts';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationParentItem = 'Profile';

    protected static ?string $navigationLabel = 'Contracts';
    protected static ?string $title = 'Contracts';


    public function table(Table $table): Table
    {
        return $table
            ->query(Contract::query()
                ->with(['business', 'team', 'contractType', 'planning', 'documents'])
                ->where('user_id', \Auth::id()))
            ->columns([
                // TODO: change storage url
                TextColumn::make('contractType.name'),
                TextColumn::make('business.name'),
                TextColumn::make('team.name'),
                TextColumn::make('planning.name'),
                TextColumn::make('start')->date(),
                TextColumn::make('end')->date(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
