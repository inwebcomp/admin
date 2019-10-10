<?php

namespace InWeb\Admin\App;

use Closure;
use Illuminate\Support\Str;

class ResourceTool extends Panel
{
    /**
     * The resource tool element.
     *
     * @var \InWeb\Admin\App\Element
     */
    public $element;

    /**
     * The resource tool's component.
     *
     * @var string
     */
    public $component;

    protected $inline = true;

    /**
     * Create a new resource tool instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct($this->name(), [new ResourceToolElement($this->component())]);

        $this->element = $this->data[0];
    }

    /**
     * Create a new resource tool instance.
     *
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }

    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Str::title(Str::snake(class_basename(get_class($this)), ' '));
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return $this->component ?? Str::kebab(class_basename(get_class($this)));
    }

    /**
     * Set the callback to be run to authorize viewing the card.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function canSee(Closure $callback)
    {
        $this->element->canSee($callback);

        return $this;
    }

    /**
     * Set additional meta information for the resource tool.
     *
     * @param  array  $meta
     * @return $this
     */
    public function withMeta(array $meta)
    {
        $this->element->withMeta($meta);

        return $this;
    }

    /**
     * Dynamically proxy method calls to meta information.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        return $this->withMeta([$method => ($parameters[0] ?? true)]);
    }

    /**
     * Prepare the panel for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), $this->element->meta());
    }
}
