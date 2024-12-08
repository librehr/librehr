<?php

namespace App\Filament\App\Pages\MyProfile;

use App\Models\User;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;


class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static string $view = 'filament.pages.profile';

    protected static ?int $navigationSort = 10;


    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(\Auth::user()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('attributes.default_business'),
                Section::make('Account')
                    ->description('')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('attributes.firstName')
                            ->required(),
                        TextInput::make('attributes.secondName'),
                        TextInput::make('email')
                            ->email()
                            ->columnSpanFull()
                            ->required()
                            ->unique(),
                        Placeholder::make('Change your Password')->content('For security reasons, to change your password
                             you must request the a password reset.')->columnSpanFull(),
                        Actions::make([
                            Action::make('Request Password Reset')
                                ->color('gray')
                                ->icon('heroicon-m-lock-closed')
                                ->requiresConfirmation()
                                ->action(function () {
                                    $user = \Auth::user();
                                    $token = app('auth.password.broker')->createToken($user);
                                    $notification = new \Filament\Notifications\Auth\ResetPassword($token);
                                    $notification->url = \Filament\Facades\Filament::getResetPasswordUrl($token, $user);
                                    $user->notify($notification);

                                    Notification::make()
                                        ->title('Password requested successfully')
                                        ->success()
                                        ->send()
                                        ->getDatabaseMessage();

                                    \Auth::logout();
                                    $this->redirect($notification->url);

                                    $recipient = auth()->user();

                                    $recipient->notify(
                                        Notification::make()
                                            ->title('Saved successfully')
                                            ->toDatabase(),
                                    );
                                }),
                        ])->columnSpanFull(),
                ])->columns(3),

                $this->personalForm()
            ])
            ->statePath('data');
    }

    public function edit(): void
    {
        User::query()
            ->where('id', \Auth::id())
            ->update($this->form->getState());

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }

    /**
     * @return Section
     * @throws \Exception
     */
    public static function personalForm(): Section
    {
        return Section::make('Personal')
            ->schema([
                FileUpload::make('attributes.avatar')
                    ->label('Photo')
                    ->image()
                    ->imageEditor()
                    ->imageEditorMode(2),
                DatePicker::make('birthday')->required(),
                TextInput::make('attributes.phone1')->required(),
                TextInput::make('attributes.phone2'),
                TextInput::make('attributes.blood_type'),
                TextInput::make('attributes.email')
                    ->email()
                    ->label(
                        'Personal Email'
                    ),
                Textarea::make('attributes.address')->columnSpanFull(),

                Repeater::make('attributes.children')
                    ->label('Children')
                    ->schema([
                        TextInput::make('name')->required(),
                        DatePicker::make('birthday')->required(),
                    ])
                    ->columnSpanFull(2)
            ])->columns(3);
    }
}
