<?php

namespace InWeb\Admin\App;

use JsonSerializable;
use InWeb\Admin\App\Resources\Resource;
use Illuminate\Http\Resources\MergeValue;

class Panel extends MergeValue implements JsonSerializable
{
    /**
     * The name of the panel.
     *
     * @var string
     */
    public $name;
    /**
     * The panel fields.
     *
     * @var array
     */
    public $data;
    /**
     * Is detail view in inline mode or column
     *
     * @var boolean
     */
    protected $inline = false;

    /**
     * Create a new panel instance.
     *
     * @param  string         $name
     * @param  \Closure|array $fields
     * @return void
     */
    public function __construct($name, $fields = [])
    {
        $this->name = $name;

        parent::__construct($this->prepareFields($fields));
    }

    public function inline($value = true)
    {
        $this->inline = $value;
        return $this;
    }

    /**
     * Prepare the given fields.
     *
     * @param  \Closure|array $fields
     * @return array
     */
    protected function prepareFields($fields)
    {
        return collect(is_callable($fields) ? $fields() : $fields)->each(function ($field) {
            $field->panel = $this->name;
        })->all();
    }

    /**
     * Get the default panel name for the given resource.
     *
     * @param Resource $resource
     * @return string
     */
    public static function defaultNameFor(Resource $resource)
    {
        return __('Базования информация');
    }

    /**
     * Prepare the panel for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'component' => 'panel',
            'name'      => $this->name,
            'inline'      => $this->inline,
            'fields'    => []
        ];
    }
}
