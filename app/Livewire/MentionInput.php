<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class MentionInput extends Component
{

    public $mentionables;
    public $body;


    public function mount()
    {
        $this->mentionables = User::all()
            ->map(function ($user) {
                return [
                    'key' => $user->name,
                    'value' => $user->name,
                    'email' => $user->email,
                ];
            });
    }

    public function render()
    {
        return view('livewire.mention-input');
    }
}
