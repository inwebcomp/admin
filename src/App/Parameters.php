<?php

namespace InWeb\Admin\App;

use Session;

class Parameters
{
    public static function remember($request, $resource, $param)
    {
        $key = self::getKey($resource, $param);

        $value = $request->input($param);

        if ($value === null)
            $value = Session::get($key);
        else if ($value === '___default') {
            Session::remove($key);
            $value = null;
        } else
            Session::put($key, $value);

        return $value;
    }

    public static function get($resource, $param)
    {
        $key = self::getKey($resource, $param);

        return Session::get($key);
    }

    public static function remove($resource, $param)
    {
        $key = self::getKey($resource, $param);

        return Session::remove($key);
    }

    /**
     * @param $resource
     * @param $param
     * @return string
     */
    public static function getKey($resource, $param)
    {
        return 'parameters::' . $resource . '::' . $param;
    }
}