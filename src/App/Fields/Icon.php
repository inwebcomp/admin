<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Traits\Macroable;

class Icon extends Field
{
    use Macroable;
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'icon-field';
    public $classes = [
        'w-1',
        'py-1',
        'px-1',
        'min-w-0',
    ];

    public function icon($icon, $img = false)
    {
        return $this->withMeta([
            'icon' => $icon,
            'img'  => $img,
        ]);
    }

    public function title($title)
    {
        return $this->withMeta(['title' => $title]);
    }

    public function color($color)
    {
        return $this->withMeta(['color' => $color]);
    }

    public function background($background)
    {
        return $this->withMeta(['background' => $background]);
    }

    public function html($html = true)
    {
        return $this->withMeta(['html' => $html]);
    }
}
