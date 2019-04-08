<?php

namespace InWeb\Admin\App\Http\Controllers\Fields;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InWeb\Admin\App\Http\Controllers\Controller;
use InWeb\Admin\App\Http\Requests\ResourceDetailRequest;

class TreeFieldController extends Controller
{
    public function tree(ResourceDetailRequest $request)
    {
        $model = \App\Admin\Resources\Category::$model;

        $item = $model::find($request->input('id'));

        return [
            'tree' => $this->getTree($model, $item),
            'item' => $item
        ];
    }

    private function getTree($model, $object = null)
    {
        if (! $object) {
            $tree = $model::whereIsRoot()->get();

            return $tree->map(function($item) {
                return [
                    'id' => $item->getKey(),
                    'title' => $item->title,
                    'level' => 1,
                    'isLeaf' => $item->isLeaf(),
                    'active' => false,
                ];
            });
        }

        $tree = $object->ancestors()->get();

        $n = 1;
        foreach ($tree as $item) {
            $item->level = $n++;
            $item->ancestor = true;
        }

        $object->active = true;

        if (! $object->isLeaf()) {
            $object->level = $n++;
            $object->ancestor = true;
            $tree[] = $object;
        }

        $cats = $object->isLeaf() ? $object->siblingsAndSelf()->get() : $object->children()->get();

        foreach ($cats as $item) {
            $item->level = $n;
            if ($item->id == $object->id) {
                $item->active = true;
            }
            $tree->push($item);

            if ($item->id == $object->id and ! $object->isLeaf()) {
                foreach ($object->descendants()->get() as $item2) {
                    $item2->level = $n + 1;
                    $tree->push($item2);
                }
            }
        }

        return $tree->map(function($item) use ($object) {
            return [
                'id' => $item->getKey(),
                'title' => $item->title,
                'level' => $item->level,
                'ancestor' => $item->ancestor,
                'isLeaf' => $item->isLeaf(),
                'active' => $item->active,
            ];
        });
    }
}