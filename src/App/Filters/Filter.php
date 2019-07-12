<?php

namespace InWeb\Admin\App\Filters;

use Closure;
use JsonSerializable;
use InWeb\Admin\App\Admin;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use InWeb\Admin\App\ProxiesCanSeeToGate;

abstract class Filter implements JsonSerializable
{
    use ProxiesCanSeeToGate;

    /**
     * The displayable name of the filter.
     *
     * @var string
     */
    public $name;

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * The meta data for the filter.
     *
     * @var array
     */
    public $meta = [];

    /**
     * The callback used to authorize viewing the filter.
     *
     * @var \Closure|null
     */
    public $seeCallback;

    public $withNull = true;

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract public function apply(Request $request, $query, $value);

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    abstract public function options(Request $request);

    /**
     * Determine if the filter should be available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToSee(Request $request)
    {
        return $this->seeCallback ? call_user_func($this->seeCallback, $request) : true;
    }

    /**
     * Set the callback to be run to authorize viewing the filter.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function canSee(Closure $callback)
    {
        $this->seeCallback = $callback;

        return $this;
    }

    /**
     * Get the component name for the filter.
     *
     * @return string
     */
    public function component()
    {
        return $this->component;
    }

    /**
     * Get the displayable name of the filter.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Admin::humanize($this);
    }

    /**
     * Get the key for the filter.
     *
     * @return string
     */
    public function key()
    {
        return get_class($this);
    }

    /**
     * Set the default options for the filter.
     *
     * @return mixed
     */
    public function default()
    {
        return null;
    }

    /**
     * Get additional meta information to merge with the filter payload.
     *
     * @return array
     */
    public function meta()
    {
        return $this->meta;
    }

    /**
     * Set additional meta information for the filter.
     *
     * @param  array  $meta
     * @return $this
     */
    public function withMeta(array $meta)
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    /**
     * Prepare the filter for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $container = Container::getInstance();

        $options = collect($this->options($container->make(Request::class)));

        if ($this->withNull)
            $options->prepend('', '—');

        return array_merge([
            'class' => $this->key(),
            'name' => $this->name(),
            'component' => $this->component(),
            'options' => $options->map(function ($value, $key) {
                return ['title' => $key, 'value' => $value];
            })->values()->all(),
            'currentValue' => $this->default(),
        ], $this->meta());
    }
}
