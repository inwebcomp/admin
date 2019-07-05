<?php

namespace InWeb\Admin\App;

use Illuminate\Support\Str;

abstract class Tool extends Element
{
    public static $position = null;

    public static $displayInNavigation = true;

    /**
     * Perform any tasks that need to happen on tool registration.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the URI route name
     *
     * @return string
     */
    public static function route()
    {
        return 'admin-' . self::uriKey();
    }

    /**
     * Get the position in menu
     *
     * @return int
     */
    public static function position()
    {
        return static::$position;
    }

    public function setPosition($position)
    {
        static::$position = $position;
        return $this;
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return Str::plural(class_basename(get_called_class()));
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return Str::plural(Str::snake(class_basename(get_called_class()), '-'));
    }

    public static function info()
    {
        return [
            'uriKey'        => static::uriKey(),
            'label'         => static::label(),
            'position'      => static::position(),
        ];
    }
}
