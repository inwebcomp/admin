<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Traits\Macroable;

class Image extends Field
{
    use Macroable;

    public $component = 'image-field';
    public $textAlign = 'center';
    public $classes = [
        'w-12',
        'py-1',
        'px-0'
    ];

    public function preview($callback)
    {
        return $this->resolveUsing($callback);
    }

    public function many()
    {
        $this->classes = [
            'py-1',
            'px-0'
        ];

        $this->textAlign = 'left';

        return $this;
    }
}
