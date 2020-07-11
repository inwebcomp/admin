<?php

namespace InWeb\Admin\App\Http\Controllers\Fields;

use InWeb\Admin\App\Fields\Contracts\TreeFieldModel;
use InWeb\Admin\App\Http\Controllers\Controller;
use InWeb\Admin\App\Http\Requests\ResourceDetailRequest;
use InWeb\Admin\App\Parameters;
use Spatie\EloquentSortable\Sortable;

class TreeFieldController extends Controller
{
    public function tree(ResourceDetailRequest $request)
    {
        $model = $request->input('model', $request->model());
        $related = $request->input('related', false);
        $rememberParent = $request->input('rememberParent', false);

        $item = $model::withoutGlobalScopes()->find($request->input('id'));

        $parent = Parameters::get($request->resource(), 'parent');

        if (! $item)
            $item = ! $rememberParent ? $item : ($parent ? $model::find($parent) : null);

        return [
            'tree' => $this->getTree($model, $item, $related),
            'item' => $item,
        ];
    }

    private function getTree($model, $object = null, $related = false)
    {
        $relatedModel = $related ? $model::find($related) : false;

        if (! $object) {
            if (! $relatedModel) {
                $query = $model::whereIsRoot()->withoutGlobalScopes();
            } else {
                $query = $relatedModel->children()->withoutGlobalScopes();
            }

            if ((new $model) instanceof Sortable)
                $query->ordered();

            $tree = $query->get();

            return $tree->map(function ($item) {
                return [
                    'id'     => $item->getKey(),
                    'title'  => $item->title,
                    'level'  => 1,
                    'isLeaf' => $item->isLeaf(),
                    'active' => false,
                ];
            });
        }

        $query = $object->ancestors()->withoutGlobalScopes()->defaultOrder();

        if ((new $model) instanceof Sortable)
            $query->ordered();

        $tree = $query->get();

        if ($relatedModel) {
            $relatedIds = $relatedModel->ancestors()->pluck('id')->push($relatedModel->id)->toArray();
            $tree = $tree->filter(function ($item) use ($relatedIds) {
                return ! in_array($item->id, $relatedIds);
            });
        }

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

        $cats = $object->isLeaf() ? $object->siblingsAndSelf() : $object->children();

        $cats->withoutGlobalScopes();

        if ($object instanceof Sortable)
            $cats->ordered();

        $cats = $cats->get();

        foreach ($cats as $item) {
            $item->level = $n;
            if ($item->id == $object->id) {
                $item->active = true;
            }
            $tree->push($item);

            if ($item->id == $object->id and ! $object->isLeaf()) {
                $query = $object->descendant()->defaultOrder()->withoutGlobalScopes();

                if ($object instanceof Sortable)
                    $query->ordered();

                foreach ($query->get() as $item2) {
                    $item2->level = $n + 1;
                    $tree->push($item2);
                }
            }
        }

        return $tree->map(function ($item) use ($object) {
            return [
                'id'       => $item->getKey(),
                'title'    => $item->title,
                'level'    => $item->level,
                'ancestor' => $item->ancestor,
                'isLeaf'   => $item->isLeaf(),
                'active'   => $item->active,
            ];
        });
    }
}