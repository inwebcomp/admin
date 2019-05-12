<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Traits\Macroable;

class Text extends Field
{
    use Macroable;

    public $component = 'text-field';

    public function link($url)
    {
        return $this->withMeta(['link' => $url]);
    }

    public function subtitle($text)
    {
        return $this->withMeta(['subtitle' => $text]);
    }
}
