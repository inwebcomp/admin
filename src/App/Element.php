<?php

namespace InWeb\Admin\App;

use Illuminate\Http\Request;

class Element implements \JsonSerializable
{
    use ProxiesCanSeeToGate;

    /**
     * The element's component.
     *
     * @var string
     */
    public $component;

    /**
     * If shoulf prefix component name with view name.
     *
     * @var string
     */
    public $prefixComponent = true;

    /**
     * The callback used to authorize viewing the card.
     *
     * @var \Closure|null
     */
    public $seeCallback;

    /**
     * Indicates if the element is only shown on the detail screen.
     *
     * @var bool
     */
    public $onlyOnDetail = false;

    /**
     * The meta data for the element.
     *
     * @var array
     */
    public $meta = [];

    /**
     * Create a new element.
     *
     * @param  string|null  $component
     * @return void
     */
    public function __construct($component = null)
    {
        $this->component = $component ?? $this->component;
    }

    /**
     * Determine if the element should be displayed for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorize(Request $request)
    {
        return $this->authorizedToSee($request);
    }

    /**
     * Set the callback to be run to authorize viewing the card.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function canSee(\Closure $callback)
    {
        $this->seeCallback = $callback;

        return $this;
    }

    /**
     * Determine if the card should be available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToSee(Request $request)
    {
        return $this->seeCallback ? call_user_func($this->seeCallback, $request) : true;
    }

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return $this->component;
    }

    /**
     * Get additional meta information to merge with the element payload.
     *
     * @return array
     */
    public function meta()
    {
        return $this->meta;
    }

    /**
     * Set additional meta information for the element.
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
     * Prepare the element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge([
            'component' => $this->component(),
            'prefixComponent' => false,
        ], $this->meta());
    }
}
