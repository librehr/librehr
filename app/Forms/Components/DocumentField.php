<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class DocumentField extends Field
{
    protected string $view = 'forms.components.document-field';

    public function openDocument()
    {
        dd('hola');
    }
}
