<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use App\Services\Notifications;
use App\Services\Reactions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;


class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $label = 'Message';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                RepeatableEntry::make('attributes.files')
                    ->label('Files')
                    ->schema([
                        TextEntry::make('')
                            ->icon('heroicon-m-arrow-down-tray')
                            ->formatStateUsing(fn ($record, $state) => data_get($record, 'attributes.fileNames')[$state] ?? null)
                            ->url(
                            fn ($state) =>
                            \Storage::url($state),
                            true
                        )->extraAttributes([
                            'target' => '_blank'
                        ])->columnSpanFull(1)
                ])->hidden(fn ($record) => empty(data_get($record, 'attributes.files', []) ))
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')->default(\Auth::id()),
                Forms\Components\RichEditor::make('attributes.body')
                    ->label('Message')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('attributes.files')
                    ->multiple()
                    ->previewable(false)
                    ->storeFileNamesIn('attributes.fileNames')
                    ->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        $userId = \Auth::id();

        return $table
            ->recordTitleAttribute(' ')
            ->columns([
                Tables\Columns\IconColumn::make('attributes.files.0')
                    ->label('Has Files?')
                    ->boolean(),
                    Tables\Columns\TextColumn::make('attributes.body')
                        ->label('Message')
                        ->html()
                        ->columnSpanFull()
                        ->description(fn ($record) => data_get($record, 'user.name') . ' - ' . data_get($record, 'created_at')->format('F Y, H:s'))
                        ->label('Message')
                        ->grow(),



            ])
            ->filters([])
       
            ->actions([
                $this->getReactionAction($userId, 'check'),
                $this->getReactionAction($userId, 'face-smile'),
                $this->getReactionAction($userId, 'face-frown'),
                $this->getReactionAction($userId, 'rocket-launch'),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->authorize(fn ($record) => $record->user_id === \Auth::id()),
                    Tables\Actions\DeleteAction::make()->authorize(fn ($record) => $record->user_id === \Auth::id()),
                    Tables\Actions\ViewAction::make()->authorize(true),
                ])->color(Color::Gray),
            ], position: Tables\Enums\ActionsPosition::AfterColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @param $userId
     * @param $type
     * @return Tables\Actions\Action
     */
    protected function getReactionAction($userId, $type): Tables\Actions\Action
    {
        return Tables\Actions\Action::make($type)
            ->size('xs')
            ->iconButton()
            ->tooltip(fn ($record) => implode(', ', data_get($record, 'attributes.reactions.' . $type, [])))
            ->iconSize('lg')
            ->badgeColor(Color::Green)
            ->label('')
            ->icon('heroicon-m-' . $type)
            ->color(Color::Gray)
            ->badge(fn ($record) => count(data_get($record, 'attributes.reactions.' . $type, [])) ?: null)
            ->action(function ($record) use ($userId, $type) {
                [$attributes, $added] = app(Reactions::class)->addReaction(
                    data_get($record, 'attributes'),
                    $userId,
                    $type
                );

                $record->attributes = $attributes;
                $record->save();

                if ($added) {
                    $record->load('task');
                    $record->reaction = $type;
                    $record->reaction_user = \Auth::user();
                    $record->reaction_type = app(Reactions::class)->parseReactionNames($type);

                    Notifications::notify(
                        Notifications\Resources\ReactionAdded::class,
                        $record,
                        data_get($record, 'user_id')
                    );
                }

            });
    }
}
