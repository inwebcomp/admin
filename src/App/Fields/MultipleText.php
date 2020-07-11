<?php

namespace InWeb\Admin\App\Fields;

/**
 * Class MultipleText
 * @package InWeb\Admin\App\Fields
 * @todo Make not translatable fields support
 */
class MultipleText extends Text
{
    public $component = 'multiple-text-field';

    public function inlineTranslationFields($value = true)
    {
        return $this->withMeta(['inlineTranslationFields' => $value]);
    }
}
