<?php

namespace InWeb\Admin\App\Fields;

use InWeb\Base\Contracts\Nested;

class TreeField extends Field
{
    public $component = 'tree-field';
    public $fullCell = true;

    public $size = 'w-full';

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->resolveUsing(function ($value, $model) {
            if ($model instanceof Nested and $model->isLeaf())
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

    public function related($modelId)
    {
        return $this->withMeta(['related' => $modelId]);
    }
}
