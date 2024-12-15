<?php

namespace App\Filament\App\Pages\MyProfile;

use App\Models\ContractTool;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ProfileTools extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.my-profile.profile-tools';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationParentItem = 'Profile';

    protected static ?string $navigationLabel = 'Tools';
    protected static ?string $title = 'Tools';


    public function table(Table $table): Table
    {
        return $table
            ->query(ContractTool::query()
                ->with(['tool', 'contract'])
                ->whereIn('contract_id', \Auth::user()->contracts->pluck('id')))
            ->columns([
                TextColumn::make('tool.name'),
                TextColumn::make('name'),
                TextColumn::make('description'),
                TextColumn::make('delivered_at')->date(),
                TextColumn::make('deliveredBy.name'),
                TextColumn::make('returned_at')->date(),
                TextColumn::make('returnedTo.name'),
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
