<?php

namespace InWeb\Admin\App\Http\Controllers\Fields;

use InWeb\Media\WithContentImages;
use InWeb\Admin\App\Http\Controllers\Controller;
use InWeb\Admin\App\Http\Requests\ResourceDetailRequest;

class EditorFieldController extends Controller
{
    public function image(ResourceDetailRequest $request)
    {
        /** @var WithContentImages $model */
        $model = $request->findModelOrFail();

        $request->validate([
            'file' => 'required|max:' . 1024 * 2 . '|mimes:jpg,jpeg,png,gif,svg',
        ]);

        $path = $request->file('file')->storePublicly($model->contentImagesPath(), ['disk' => 'public']);

        return [
            'link' => \Storage::url($path)
        ];
    }

    public function file(ResourceDetailRequest $request)
    {
        /** @var WithContentImages $model */
        $model = $request->findModelOrFail();

        $request->validate([
            'file' => 'required|max:' . 1024 * 20,
        ]);

        $path = $request->file('file')->storePubliclyAs($model->contentImagesPath(), $request->file('file')->getClientOriginalName(), ['disk' => 'public']);

        return [
            'link' => \Storage::url($path)
        ];
    }
}