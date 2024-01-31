<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\AbsenceResource;
use App\Filament\Resources\ContractResource;
use App\Filament\Resources\UserResource;
use App\Models\Contract;
use App\Models\ContractTool;
use App\Models\Tool;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageUserTools extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'tools';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Tools';
    }

    public function getHeaderActions(): array
    {
        return [
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        $record = $this->getRecord();

        return $table
            ->recordTitleAttribute('type.name')
            ->columns(
                [
                    Tables\Columns\TextColumn::make('tool.name'),
                    Tables\Columns\TextColumn::make('name')
                        ->description(fn ($record) => data_get($record, 'description')),
                    Tables\Columns\TextColumn::make('deliveredBy.name'),
                    Tables\Columns\TextColumn::make('returnedTo.name'),
                    Tables\Columns\TextColumn::make('delivered_at')->dateTime(),
                    Tables\Columns\TextColumn::make('returned_at')->dateTime(),
                ]
            )
            ->filters([

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make('Register Tool')
                    ->requiresConfirmation()
                    ->form(function () use ($record) {
                        return [
                            Forms\Components\Select::make('tool_id')
                                ->label('Tool')
                            ->options(function () {
                                return Tool::query()->pluck('name', 'id');
                            }),
                            Forms\Components\Select::make('contract_id')
                                ->label('Contract')
                                ->options(function () use($record) {
                                    return Contract::query()
                                        ->with('business')
                                        ->where('user_id', $record->id)
                                        ->get()
                                        ->pluck('business.name', 'id')
                                        ->unique();
                                }),
                            Forms\Components\TextInput::make('name'),
                            Forms\Components\DatePicker::make('delivered_at'),
                            Forms\Components\Hidden::make('delivered_by')
                            ->default(\Auth::id()),
                            Forms\Components\Textarea::make('description')
                                ->helperText('Give any extra information.')
                        ];
                    })->action(function ($data) {
                        ContractTool::query()->create($data);

                        Notification::make()
                            ->title('Created successfully')
                            ->success()
                            ->send();
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
