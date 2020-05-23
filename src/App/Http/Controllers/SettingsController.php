<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Http\Request;
use InWeb\Admin\App\Actions\Action;
use InWeb\Admin\App\Parameters;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        
        if (! in_array($field, $this->availableFields()))
            return abort(404, __('Wrong setting'));

        Parameters::remember($request, 'admin', $field, $value);

        return Action::message(__('Настройки админ-панели изменены'));
    }

    private function availableFields()
    {
        return ['language'];
    }
}
