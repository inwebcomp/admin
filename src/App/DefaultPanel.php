<?php

namespace InWeb\Admin\App;

use InWeb\Admin\App\Http\Requests\AdminRequest;

class DefaultPanel extends Panel
{
    public function __construct(AdminRequest $request, $fields = [])
    {
        parent::__construct(self::defaultNameFor($request->newResource()), $fields);
    }
}
