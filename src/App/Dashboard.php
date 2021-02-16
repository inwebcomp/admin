<?php

namespace InWeb\Admin\App;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Dashboard extends Element
{
    use AuthorizedToSee,
        Metable,
        Makeable,
        ProxiesCanSeeToGate,
        WithNotification;

    public static $position = 999;
    public static $group = 'statistics';

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
     * Get the displayable name of the dashboard.
     *
     * @return string
     */
    public static function label()
    {
        return Str::singular(class_basename(get_called_class()));
    }

    /**
     * Get the URI key of the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return Str::singular(Str::snake(class_basename(get_called_class()), '-'));
    }

    /**
     * Determine if this resource is available for navigation.
     *
     * @param Request $request
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
        return 'dashboard';
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
}
