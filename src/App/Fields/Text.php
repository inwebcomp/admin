<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Traits\Macroable;

class Text extends Field
{
    use Macroable;

    public $component = 'text-field';

    public function linkClasses($classes)
    {
        return $this->withMeta(['linkClasses' => $classes]);
    }

    public function link($url)
    {
        return $this->withMeta(['link' => $url]);
    }

    public function subtitle($text)
    {
        return $this->withMeta(['subtitle' => $text]);
    }

    public function disabled()
    {
        $this->disabled = true;

        return $this->withMeta(['extraAttributes' => ['disabled' => true]]);
    }

    public function asHtml($value = true)
    {
        return $this->withMeta(['asHtml' => $value]);
    }
}
