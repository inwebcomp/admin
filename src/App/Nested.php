<?php

namespace InWeb\Admin\App;

trait Nested
{
    public function breadcrumbs(\App\Contracts\Nested $node = null)
    {
        $path = [
            self::root()
        ];

        if (! $node)
            return $path;

        $node->ancestors()->each(function(\App\Contracts\Nested $ancestor) use (&$path) {
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