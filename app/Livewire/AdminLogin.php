<?php

namespace App\Livewire;

use Livewire\Component;

class AdminLogin extends Component
{
    public bool $admin = false;
    public bool $adminPanel = false;

    public function mount()
    {
        $this->admin = \Auth::user()->role->name === 'admin';
        if (request()->routeIs('filament.admin.pages.dashboard')) {
            $this->adminPanel = true;
        }
    }

    public function render()
    {
        return view('livewire.admin-login');
    }
}
