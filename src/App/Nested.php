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

    public function breadcrumbs(ResourceIndexRequest $request, NestedContract $node = null, $withOptions = true)
    {
        $path = $this->breadcrumbsPath($node);

        if ($withOptions) {
            $options = $node ? $node->children() : $this->nestedRelationResource()->whereIsRoot();

//            $options->hasChildren();

            static::indexQuery($request, $options);

            $options = $options->withoutGlobalScopes()->ordered();

            if ($options->getModel()->translatable())
                $options->withTranslation();

            $options = $options->get()->map(function($item) {
                return [
                    'title' => $item->title,
                    'value' => $item->getKey(),
                ];
            })->toArray();

            array_unshift($options, [
                'title' => '-- ' . __('Выберите значение'),
                'value' => null,
            ]);
        } else {
            $options = null;
        }

        return [
            'path' => $path,
            'options' => $options,
        ];
    }

    public function breadcrumbsPath(NestedContract $node = null)
    {
        $path = [
            self::root()
        ];

        if (! $node)
            return $path;

        $node->ancestors()->withoutGlobalScopes()->defaultOrder()->ordered()->each(function(NestedContract $ancestor) use (&$path) {
            $path[] = [
                'title' => $ancestor->title,
                'id' => $ancestor->getKey()
            ];
        });

        $path[] = [
            'title' => $node->title,
            'id' => $node->getKey()
        ];

        return $path;
    }

    private static function root() {
        return [
            'title' => __('Корень'),
            'id' => '___default',
            'root' => true,
        ];
    }
}