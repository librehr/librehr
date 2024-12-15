<?php

namespace App\Livewire;

use Livewire\Component;

class HeaderNotifications extends Component
{
    public $businesses = null;

    public function mount()
    {

    }

    public function render()
    {
        return  <<<'HTML'
        <span>

</span>
HTML;

        HTML;

        return <<<'HTML'
        <div class="bg-gray-200 text-center py-2 text-xs font-semibold">
            You have {{ Auth::user()->unreadNotifications->count() }} unread notifications!
        </div>
        HTML;
    }
}
