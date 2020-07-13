<?php

namespace InWeb\Admin\App;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Tool extends Element
{
    use WithNotification;

    public static $position = 999;
    public static $group = 'tools';

    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return static::$group;
    }

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
     * Determine if this resource is available for navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return true;
    }

    /**
     * Get the URI route name
     *
     * @return string
     */
    public static function route()
    {
        return static::uriKey();
    }

    /**
     * Get color of menu item
     *
     * @return string|null
     */
    public static function color()
    {
        return null;
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
