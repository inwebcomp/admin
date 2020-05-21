<?php

namespace InWeb\Admin\App;

use Illuminate\Support\Traits\Macroable;
use InWeb\Admin\App\Fields\FieldElement;

class Section extends FieldElement
{
    use Macroable;

    public $component = 'section';

    public $prefixComponent = true;

    public $name;

    /**
     * Create a new field.
     *
     * @param  string      $name
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Create a new element.
     *
     * @param array $arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge([
            'component'       => $this->component(),
            'prefixComponent' => true,
            'panel'           => $this->panel,
            'name'            => $this->name,
        ], $this->meta());
    }
}
