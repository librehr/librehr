<?php

namespace App\Filament\App\Resources\ContractResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\App\Resources\ContractResource;
use App\Models\Contract;
use App\Models\ContractTool;
use App\Models\Tool;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;

class Tools extends ManageRelatedRecords
{
    protected static string $resource = ContractResource::class;

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
            ->schema([
                Forms\Components\DateTimePicker::make('returned_at'),
            ]);
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
                            })->required(),
                            Forms\Components\Hidden::make('contract_id')
                                ->default(fn () => data_get($this, 'record.id')),
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\DatePicker::make('delivered_at')->required(),
                            Forms\Components\Hidden::make('delivered_by')->default(\Auth::id()),
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
