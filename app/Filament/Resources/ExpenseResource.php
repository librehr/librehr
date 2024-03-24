<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\Widgets\UserExpenseMonth;
use App\Models\Contract;
use App\Models\Expense;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        $user = \Auth::user();
        $control = str($form->getLivewire()->getName())->contains('expense-control');
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Grid::make()
                        ->schema([
                                Forms\Components\Hidden::make('business_id')
                                    ->default($user->getActiveBusinessId()),
                                self::getContractForm($control, $user),
                                Forms\Components\DatePicker::make('date')
                                    ->required(),
                                Forms\Components\TextInput::make('amount')
                                    ->required()
                                    ->numeric()
                        ])
                        ->columns(3),

                    Forms\Components\Textarea::make('attributes.note')->columnSpanFull(),
                    Forms\Components\FileUpload::make('attributes.files')->columnSpanFull()
                    ->multiple(),
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        $control = str($table->getLivewire()->getName())->contains('expense-control');

        return $table
            ->groups([
                'contract.user.name'
            ])
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contract.user.name')
                    ->label('Employee')
                    ->visible($control),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested')
                    ->tooltip(fn ($state) => $state->format('F d, Y H:i'))
                    ->formatStateUsing(fn ($record) => $record->created_at->diffForHumans()),
                Tables\Columns\TextColumn::make('date')
                    ->label('Expense Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('paid')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true) ,
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Deny')
                    ->icon('heroicon-m-x-circle')
                    ->button()
                    ->action(function ($record) {
                        $record->status = 'denied';
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $control && in_array($record->status->value, [
                            'pending'
                        ])),
                Tables\Actions\Action::make('Accept')
                    ->icon('heroicon-m-check')
                    ->color(Color::Blue)
                    ->button()
                    ->action(function ($record) {
                        $record->status = 'accepted';
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $control && !in_array($record->status->value, [
                            'accepted', 'cancelled', 'paid', 'denied'
                        ])),
                Tables\Actions\Action::make('Mark paid')
                    ->icon('heroicon-m-banknotes')
                    ->color(Color::Green)
                    ->button()
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'paid';
                        $record->paid = true;
                        $record->paid_at = now();
                        $record->save();
                    })
                ->visible(fn ($record) => $control && $record->status->value === 'accepted'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Cancel')
                    ->icon('heroicon-m-x-circle')
                    ->action(function ($record) {
                        $record->status = 'cancelled';
                        $record->save();
                    })
                ->visible(fn ($record) => $record->status->value === 'pending' && $control === false),
                ])
            ->bulkActions([

            ])->modifyQueryUsing(fn ($query) => $control ? null : $query->where('contract_id', \Auth::user()->getActiveContractId())
                ->orderByDesc($control === false ? 'created_at' : 'date'));
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
            UserExpenseMonth::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    protected static function getContractForm($control, $user) {
        if ($control) {
            return  Forms\Components\Select::make('contract_id')
                ->label('Employeer')
                ->preload()
                ->searchable()
                ->options(fn () => Contract::query()
                    ->with('user:id,name')
                    ->get()->pluck('user.name', 'id'))
                ->visible($control);
        }

        return Forms\Components\Hidden::make('contract_id')
            ->default($user->getActiveContractId());
    }
}
