<?php

namespace InWeb\Admin\App\ResourceTools;

use App\Models\Category;
use InWeb\Admin\App\ResourceTool;

class ActionsOnModel extends ResourceTool
{
    public $component = 'actions-on-model-tool';

    public function name()
    {
        return __('Действия');
    }
}
