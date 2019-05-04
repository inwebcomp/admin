<?php

namespace InWeb\Admin\App\Http\Requests;

use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Resources\Resource;

/**
 * Trait InteractsWithTranslations
 * @package InWeb\Admin\App\Http\Requests
 * @property string|null resource
 * @property string|null resourceID
 */
trait InteractsWithTranslations
{
    protected $withTranslations = [];

    public function withTranslations($locale)
    {
        if (isset($this->withTranslations[$locale]))
            return $this->withTranslations[$locale];

        foreach ($this->input() as $attribute => $value) {
            if (preg_match("/:[a-z]{2}$/", $attribute)) {
                if ($value !== null) {
                    return $this->withTranslations[$locale] = true;
                }
            }
        }

        return $this->withTranslations[$locale] = false;
    }
}
