<?php

namespace InWeb\Admin\App\Fields;

class TreeField extends Field
{
    public $component = 'tree-field';
    public $fullCell = true;

    public $size = 'w-full';

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->resolveUsing(function ($value, $model) {
            if ($model instanceof \App\Contracts\Nested and $model->isLeaf())
                $this->withMeta(['leaf' => true]);

            return $value;
        });
    }

    /**
     * Determine wich model sould be used for select list
     *
     * @param $model
     * @return TreeField
     */
    public function list($model)
    {
        return $this->withMeta(['model' => $model]);
    }
}
