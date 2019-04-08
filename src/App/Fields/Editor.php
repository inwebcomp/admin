<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Traits\Macroable;

class Editor extends Field
{
    use Macroable;
    public $size = 'w-full';
    public $component = 'editor-field';

    public function sections($withTitle = false)
    {
        return $this->withMeta([
            'sections' => true,
            'withSectionsTitle' => $withTitle
        ]);
    }
}