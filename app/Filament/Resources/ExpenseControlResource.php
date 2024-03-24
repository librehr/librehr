<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseControlResource\Pages;
use App\Filament\Resources\ExpenseControlResource\Widgets\ExpenseControlStats;
use App\Models\Expense;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ExpenseControlResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationLabel = 'Expenses Control';
    protected static ?string $navigationIcon = 'heroicon-m-banknotes';
    protected static ?string $navigationGroup = 'Human Resources';

    protected static ?int $navigationSort = 7;
    public static function getNavigationBadge(): ?string
    {
        return Expense::query()->where('status', 'pending')->count('id') ?: null;
    }

    public static function form(Form $form): Form
    {
        return ExpenseResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return ExpenseResource::table($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ExpenseControlStats::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenseControls::route('/'),
            'create' => Pages\CreateExpenseControl::route('/create'),
            'edit' => Pages\EditExpenseControl::route('/{record}/edit'),
        ];
    }
}
