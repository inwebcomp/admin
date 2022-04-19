<?php

namespace InWeb\Admin\App;

use InWeb\Admin\App\Http\Requests\ResourceIndexRequest;
use \InWeb\Base\Contracts\Nested as NestedContract;

trait Nested
{
    public function nestedRelationResourceField()
    {
        return 'parent_id';
    }

    public function nestedRelationResource()
    {
        return $this;
    }

    public function findInDescendants()
    {
        return false;
    }

    public static function breadcrumbsType()
    {
        return static::BREADCRUMBS_CHAIN;
    }

    public function breadcrumbs(ResourceIndexRequest $request, $node = null, $withOptions = true)
    {
        $path = $this->breadcrumbsPath($node);

        $options = null;
        $relationResource = $this->nestedRelationResource();

        if ($withOptions) {
            if ($relationResource->model() instanceof NestedContract) {
                if ($node) {
                    if ($node->isLeaf())
                        $options = $node->siblingsAndSelf();
                    else
                        $options = $node->children();
                } else {
                    $options = $relationResource->whereIsRoot();
                }
            } else {
                $options = $relationResource;
//                $options = ($node) ? $relationResource->where('id', '=', '0') : $relationResource;
            }

//            $options->hasChildren();

            static::indexQuery($request, $options);

            $options = $options->withoutGlobalScopes()->ordered();

            if ($options->getModel()->translatable())
                $options->withTranslation();

            $options = $options->get()->map(function ($item) {
                return [
                    'title' => $item->title,
                    'value' => $item->getKey(),
                ];
            })->toArray();

            array_unshift($options, [
                'title' => '-- ' . __('Выберите значение'),
                'value' => null,
            ]);
        }

        return [
            'path'     => $path,
            'options'  => $options,
            'selected' => $node?->getKey(),
        ];
    }

    public function breadcrumbsPath($node = null)
    {
        $path = [
            self::root()
        ];

        if (! $node)
            return $path;

        if ($node instanceof NestedContract) {
            $node->ancestors()->withoutGlobalScopes()->defaultOrder()->ordered()->each(function (NestedContract $ancestor) use (&$path) {
                $path[] = [
                    'title' => $ancestor->title,
                    'id'    => $ancestor->getKey()
                ];
            });

            if (! $node->isLeaf()) {
                $path[] = [
                    'title' => $node->title,
                    'id'    => $node->getKey()
                ];
            }
        }

        return $path;
    }

    private static function root()
    {
        return [
            'title' => __('Корень'),
            'id'    => '___default',
            'root'  => true,
        ];
    }
}
