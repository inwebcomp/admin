<?php

namespace InWeb\Admin\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AdminRequest
 * @package InWeb\Admin\App\Http\Requests
 */
class AdminRequest extends FormRequest
{
    use InteractsWithResources,
        InteractsWithRelatedResources,
        InteractsWithTranslations;

    /**
     * @param null $attributes
     * @return self
     */
    public function prepareBooleanValues($attributes = null)
    {
        if (! $attributes)
            $attributes = array_keys($this->input());

        foreach ($attributes as $attribute) {
            if (isset($this[$attribute]))
                $this[$attribute] = $this[$attribute] == 'true';
        }

        return $this;
    }
}
