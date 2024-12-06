<?php

namespace App\Filament\Pages;


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
