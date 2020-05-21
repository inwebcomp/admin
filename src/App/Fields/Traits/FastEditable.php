<?php

namespace InWeb\Admin\App\Fields\Traits;

use InWeb\Admin\App\Fields\Field;

trait FastEditable
{
    public $fastEditProps;

    /**
     * @param bool   $value
     * @param string $component
     * @return Field|FastEditable
     */
    public function fastEdit($value = true, $component = 'text-input')
    {
        if ($value === true)
            $value = $this->attribute;

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
     * @return Field|FastEditable
     */
    public function fastEditSelect()
    {
        return $this->fastEditComponent('app-select');
    }

    /**
     * @return Field|FastEditable
     */
    public function fastEditBoolean()
    {
        return $this->fastEditComponent('switch-input');
    }

    /**
     * @param \Closure|array $props
     * @return Field|FastEditable
     */
    public function fastEditProps($props)
    {
        if (! is_callable($props))
            $this->fastEditProps = function () use ($props) {
                return $props;
            };
        else
            $this->fastEditProps = $props;
        
        return $this;
    }
}