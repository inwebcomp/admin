<?php

namespace InWeb\Admin\App\Fields;

use Closure;
use InWeb\Base\Traits\Translatable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use InWeb\Admin\App\Contracts\Resolvable;
use InWeb\Admin\App\Fields\Traits\FastEditable;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use JsonSerializable;

abstract class Field extends FieldElement implements JsonSerializable, Resolvable
{
    use FastEditable;

    /**
     * The displayable name of the field.
     *
     * @var string
     */
    public $name;
    /**
     * The attribute / column name of the field.
     *
     * @var string
     */
    public $attribute;
    /**
     * The field's resolved value.
     *
     * @var mixed
     */
    public $value;
    /**
     * The callback to be used to resolve the field's display value.
     *
     * @var \Closure
     */
    public $displayCallback;
    /**
     * The callback to be used to resolve the field's value.
     *
     * @var \Closure
     */
    public $resolveCallback;
    /**
     * The callback to be used to hydrate the model attribute.
     *
     * @var callable
     */
    public $fillCallback;
    /**
     * The validation rules for creation and updates.
     *
     * @var array
     */
    public $rules = [];
    /**
     * The validation rules for creation.
     *
     * @var array
     */
    public $creationRules = [];
    /**
     * The validation rules for updates.
     *
     * @var array
     */
    public $updateRules = [];
    /**
     * Indicates if the field should be sortable.
     *
     * @var bool
     */
    public $sortable = false;

    /**
     * Indicates if the field is nullable.
     *
     * @var bool
     */
    public $nullable = false;

    /**
     * Values which will be replaced to null.
     *
     * @var array
     */
    public $nullValues = [''];

    /**
     * Indicates if the field was resolved as a pivot field.
     *
     * @var bool
     */
    public $pivot = false;
    /**
     * Show original value
     *
     * @var bool
     */
    public $original = false;
    /**
     * The text alignment for the field's text in tables.
     *
     * @var string
     */
    public $textAlign = 'left';
    /**
     * The classes for the field's in tables.
     *
     * @var array
     */
    public $classes = [];
    /**
     * The custom components registered for fields.
     *
     * @var array
     */
    public static $customComponents = [];
    /**
     * If field should render <td> by itself in Index View
     * @var bool
     */
    public $fullCell = false;
    /**
     * Size of field
     * @var string
     */
    public $size = 'w-1/2';
    /**
     * Default value of field
     * @var mixed
     */
    public $default = null;
    /**
     * If true, field value will not be mutated
     * @var mixed
     */
    public $disabled = false;

    /**
     * Create a new field.
     *
     * @param string $name
     * @param string|null $attribute
     * @param mixed|null $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        $this->name = $name;
        $this->resolveCallback = $resolveCallback;
        $this->attribute = $attribute ?? str_replace(' ', '_', Str::lower($name));
    }

    /**
     * Create a new element.
     *
     * @param array $arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }

    /**
     * Set the help text for the field.
     *
     * @param string $helpText
     * @return $this
     */
    public function help($helpText)
    {
        return $this->withMeta(['helpText' => $helpText]);
    }

    public function size($width)
    {
        $this->size = 'w-' . $width;
        return $this;
    }

    public function default($value)
    {
        $this->default = $value;
        return $this;
    }

    public function classes($classes, $override = false)
    {
        if ($override) {
            $this->classes = $classes;
        } else {
            $this->classes = array_merge($this->classes, $classes);
        }

        return $this;
    }

    /**
     * Resolve the field's value for display.
     *
     * @param mixed $resource
     * @param string|null $attribute
     * @return void
     * @throws \ReflectionException
     */
    public function resolveForDisplay($resource, $attribute = null)
    {
        $attribute = $attribute ?? $this->attribute;

        if ($attribute === 'ComputedField') {
            return;
        }

        if (! $this->displayCallback) {
            $this->resolve($resource, $attribute);
        }

        if (is_callable($this->displayCallback)) { //  && $value !== '___missing'
            $value = $this->resolveAttribute($resource, $attribute, false);
            $this->value = call_user_func($this->displayCallback, $value);
        }
    }

    /**
     * Resolve the field's value.
     *
     * @param mixed $resource
     * @param string|null $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        $attribute = $attribute ?? $this->attribute;

        if ($attribute instanceof Closure ||
            (is_callable($attribute) && is_object($attribute))) {
            return $this->resolveComputedAttribute($attribute);
        }

        if (! $this->resolveCallback) {
            $this->value = $this->resolveAttribute($resource, $attribute);
        }

        if (is_callable($this->resolveCallback)) { //  && $value !== '___missing'
            $value = $this->resolveAttribute($resource, $attribute);
            $this->value = call_user_func($this->resolveCallback, $value, $resource);
        }
    }

    public function original($value = true)
    {
        $this->original = $value;
        return $this;
    }

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param Model $resource
     * @param string $attribute
     * @param bool $original
     * @return mixed
     * @throws \ReflectionException
     */
    protected function resolveAttribute($resource, $attribute, $original = true)
    {
        if (
            ! (new \ReflectionClass($resource))->isAnonymous() and
            $resource->translatable() && $resource->isTranslationAttribute($attribute)
        ) {
            $this->withMeta(['translatable' => true]);

            /** @var Translatable $resource */
            $values = [];
            $locales = config('inweb.languages');
            array_unshift($locales, \App::getLocale());
            $locales = array_unique($locales);

            foreach ($locales as $locale) {
                if ($locale == \App::getLocale())
                    continue;

                if ($original and $this->original)
                    $values[$locale] = optional($resource->translate($locale))->getOriginal($attribute);
                else
                    $values[$locale] = optional($resource->translate($locale))->{$attribute};
            }

            $this->withMeta(['translatableValues' => $values]);
            $this->withMeta(['currentLocale' => \App::getLocale()]);
        }

        if ($original and $this->original) {
            if (
                ! (new \ReflectionClass($resource))->isAnonymous() and
                $resource->translatable() && $resource->isTranslationAttribute($attribute)
            ) {
                return optional($resource->translate())->getOriginal($attribute) ?? $this->default;
            } else {
                return $resource->getOriginal($attribute) ?? $this->default;
            }
        }

        return $resource->{$attribute} ?? $this->default;
    }

    /**
     * Resolve a computed attribute.
     *
     * @param callable $attribute
     * @return void
     */
    protected function resolveComputedAttribute($attribute)
    {
        $this->value = $attribute();

        $this->attribute = 'ComputedField';
    }

    /**
     * Define the callback that should be used to resolve the field's value.
     *
     * @param callable $displayCallback
     * @return $this
     */
    public function displayUsing(callable $displayCallback)
    {
        $this->displayCallback = $displayCallback;

        return $this;
    }

    /**
     * Define the callback that should be used to resolve the field's value.
     *
     * @param callable $resolveCallback
     * @return $this
     */
    public function resolveUsing(callable $resolveCallback)
    {
        $this->resolveCallback = $resolveCallback;

        return $this;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param AdminRequest $request
     * @param object $model
     * @return mixed
     */
    public function fill(AdminRequest $request, $model)
    {
        return $this->fillInto($request, $model, $this->attribute);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param AdminRequest $request
     * @param object $model
     * @return mixed
     */
    public function fillForAction(AdminRequest $request, $model)
    {
        return $this->fill($request, $model);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param AdminRequest $request
     * @param object $model
     * @param string $attribute
     * @param string|null $requestAttribute
     * @return mixed
     */
    public function fillInto(AdminRequest $request, $model, $attribute, $requestAttribute = null)
    {
        return $this->fillAttribute($request, $requestAttribute ?? $this->attribute, $model, $attribute);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param AdminRequest $request
     * @param string $requestAttribute
     * @param object $model
     * @param string $attribute
     * @return void
     */
    protected function fillAttribute(AdminRequest $request, $requestAttribute, $model, $attribute)
    {
        if (isset($this->fillCallback)) {
            return call_user_func(
                $this->fillCallback, $request, $model, $attribute, $requestAttribute
            );
        }

        return $this->fillAttributeFromRequest(
            $request, $requestAttribute, $model, $attribute
        );
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param AdminRequest $request
     * @param string $requestAttribute
     * @param object $model
     * @param string $attribute
     * @return mixed
     */
    protected function fillAttributeFromRequest(AdminRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $value = $request[$requestAttribute];

            $model->{$attribute} = $this->isNullValue($value) ? null : $value;

            if (! $model instanceof Fluent and $model->translatable() and $model->isTranslationAttribute($attribute)) {
                /** @var Translatable|Model $model */

                foreach (config('inweb.languages') as $locale) {
                    if ($locale == config('app.locale'))
                        continue;

                    $translationAttribute = $requestAttribute . ':' . $locale;

                    if ($request->withTranslations($locale)) {
                        if ($request->exists($translationAttribute)) {
                            $model->{$translationAttribute} = $request[$translationAttribute];
                        }
                    } else if ($model->getTranslation($locale, false)) {
                        if ($model->getKey())
                            $model->deleteTranslations($locale);
                    }
                }
            }
        }
    }

    /**
     * Check value for null value.
     *
     * @param mixed $value
     * @return bool
     */
    protected function isNullValue($value)
    {
        if (! $this->nullable) {
            return false;
        }

        return is_callable($this->nullValues)
            ? ($this->nullValues)($value)
            : in_array($value, (array) $this->nullValues);
    }

    /**
     * Specify a callback that should be used to hydrate the model attribute for the field.
     *
     * @param callable $fillCallback
     * @return $this
     */
    public function fillUsing($fillCallback)
    {
        $this->fillCallback = $fillCallback;

        return $this;
    }

    /**
     * Set the validation rules for the field.
     *
     * @param callable|array|string $rules
     * @return $this
     */
    public function rules($rules)
    {
        $this->rules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;

        return $this;
    }

    /**
     * Get the validation rules for this field.
     *
     * @param AdminRequest $request
     * @return array
     */
    public function getRules(AdminRequest $request)
    {
        $rules = is_callable($this->rules) ? call_user_func($this->rules, $request) : $this->rules;

        $result = [
            $this->attribute => $rules,
        ];

        /** @var Model $model */
        try {
            $model = $request->findModelOrFail();
        } catch (ModelNotFoundException $ex) {
            $model = $request->model();
        }

        if ($model->translatable() and $model->isTranslationAttribute($this->attribute)) {
            /** @var Translatable $model */

            foreach (config('inweb.languages') as $locale) {
                if ($locale == config('app.locale'))
                    continue;

                if (! $request->withTranslations($locale)) {
                    foreach ($rules as $key => $rule) {
                        if ($rule == 'required') {
                            unset($rules[$key]);
                            break;
                        }
                    }
                }

                $translationAttribute = $this->attribute . ':' . $locale;
                $result[$translationAttribute] = $rules;
            }
        }

        return $result;
    }

    /**
     * Get the update rules for this field.
     *
     * @param AdminRequest $request
     * @return array
     */
    public function getUpdateRules(AdminRequest $request)
    {
        $rules = [
            $this->attribute => is_callable($this->updateRules)
                ? call_user_func($this->updateRules, $request)
                : $this->updateRules,
        ];

        return array_merge_recursive(
            $this->getRules($request), $rules
        );
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param callable|array|string $rules
     * @return $this
     */
    public function updateRules($rules)
    {
        $this->updateRules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;

        return $this;
    }

    /**
     * Get the update rules for this field.
     *
     * @param AdminRequest $request
     * @return array
     */
    public function getCreationRules(AdminRequest $request)
    {
        $rules = [
            $this->attribute => is_callable($this->creationRules)
                ? call_user_func($this->creationRules, $request)
                : $this->creationRules,
        ];

        return array_merge_recursive(
            $this->getRules($request), $rules
        );
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param callable|array|string $rules
     * @return $this
     */
    public function creationRules($rules)
    {
        $this->creationRules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;

        return $this;
    }

    /**
     * Specify that this field should be sortable.
     *
     * @param bool $value
     * @return $this
     */
    public function sortable($value = true)
    {
        if (! $this->computed()) {
            $this->sortable = $value;
        }

        return $this;
    }

    /**
     * Indicate that the field should be nullable.
     *
     * @param bool $nullable
     * @param array|Closure $values
     * @return $this
     */
    public function nullable($nullable = true, $values = null)
    {
        $this->nullable = $nullable;

        if ($values !== null) {
            $this->nullValues($values);
        }

        return $this;
    }

    /**
     * Specify nullable values.
     *
     * @param array|Closure $values
     * @return $this
     */
    public function nullValues($values)
    {
        $this->nullValues = $values;

        return $this;
    }

    /**
     * Determine if the field is computed.
     *
     * @return bool
     */
    public function computed()
    {
        return (is_callable($this->attribute) && ! is_string($this->attribute)) ||
               $this->attribute == 'ComputedField';
    }

    /**
     * Get the component name for the field.
     *
     * @return string
     */
    public function component()
    {
        if (isset(static::$customComponents[get_class($this)])) {
            return static::$customComponents[get_class($this)];
        }

        return $this->component;
    }

    /**
     * Set the component that should be used by the field in general.
     *
     * @param string $component
     * @return void
     */
    public static function useCustomComponent($component)
    {
        static::$customComponents[get_called_class()] = $component;
    }

    /**
     * Set the component that should be used by this field
     *
     * @param string $component
     * @return $this
     */
    public function useComponent($component)
    {
        $this->component = $component;
        return $this;
    }

    public function display()
    {
        $this->component = 'detail-' . $this->component;
        $this->prefixComponent = false;
        $this->disabled = true;

        return $this;
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge([
            'component'       => $this->component(),
            'prefixComponent' => $this->prefixComponent,
            'indexName'       => $this->name,
            'name'            => $this->name,
            'disabled'        => $this->disabled,
            'attribute'       => $this->attribute,
            'value'           => $this->value,
            'panel'           => $this->panel,
            'textAlign'       => $this->textAlign,
            'classes'         => $this->classes,
            'fullCell'        => $this->fullCell,
            'size'            => $this->size,
        ], $this->meta());
    }
}
