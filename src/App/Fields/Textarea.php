<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Traits\Macroable;

class Textarea extends Field
{
    use Macroable;

    public $component = 'textarea-field';
}
