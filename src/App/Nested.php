<?php

namespace InWeb\Admin\App;

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

    public function breadcrumbs(\App\Contracts\Nested $node = null, $withOptions = true)
    {
        $path = $this->breadcrumbsPath($node);

        if ($withOptions) {
            $options = $node ? $node->children() : $this->nestedRelationResource()->whereIsRoot();

//            $options->hasChildren();

            $options = $options->withoutGlobalScopes()->ordered()->get()->map(function($item) {
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

    public function breadcrumbsPath(\App\Contracts\Nested $node = null)
    {
        $path = [
            self::root()
        ];

        if (! $node)
            return $path;

        $node->ancestors()->withoutGlobalScopes()->ordered()->each(function(\App\Contracts\Nested $ancestor) use (&$path) {
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