<?php

namespace InWeb\Admin\App\Fields\Traits;

use InWeb\Admin\App\Fields\Field;

trait FastEditable
{
    public $fastEditProps = [];

    /**
     * @param bool|string $value
     * @param string      $component
     * @return Field|FastEditable
     */
    public function fastEdit($value = true, $component = 'text-input')
    {
        if ($value === true)
            $value = $this->attribute;

        $this->fastEditProps(['small' => true]);

        return $this->withMeta([
            'fastEdit'          => $value,
            'fastEditComponent' => $component
        ]);
    }

    /**
     * @param string $value
     * @return Field|FastEditable
     */
    public function fastEditComponent($value)
    {
        return $this->withMeta(['fastEditComponent' => $value]);
    }

    /**
     * @param bool|string $value
     * @return Field|FastEditable
     */
    public function fastEditSelect($value = true)
    {
        return $this->fastEdit($value, 'app-select');
    }

    /**
     * @param bool|string $value
     * @return Field|FastEditable
     */
    public function fastEditBoolean($value = true)
    {
        return $this->fastEdit($value, 'switch-input');
    }

    /**
     * @param bool|string $value
     * @param float       $step
     * @return Field|FastEditable
     */
    public function fastEditNumber($value = true, $step = 0.01)
    {
        $this->fastEditProps([
            'step' => $step,
            'type' => 'number'
        ]);

        return $this->fastEdit($value);
    }

    /**
     * @param \Closure|array $props
     * @return Field|FastEditable
     */
    public function fastEditProps($props)
    {
        if (! is_callable($props))
            $this->fastEditProps = array_merge($this->fastEditProps, $props);
        else
            $this->fastEditProps = $props;

        return $this;
    }
}