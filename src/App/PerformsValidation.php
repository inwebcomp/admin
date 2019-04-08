<?php

namespace InWeb\Admin\App;

use Illuminate\Support\Facades\Validator;
use InWeb\Admin\App\Http\Requests\AdminRequest;

trait PerformsValidation
{
    /**
     * Validate a resource creation request.
     *
     * @param AdminRequest $request
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function validateForCreation(AdminRequest $request)
    {
        static::validatorForCreation($request)->validate();
    }

    /**
     * Create a validator instance for a resource creation request.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Validation\Validator
     */
    public static function validatorForCreation(AdminRequest $request)
    {
        return Validator::make($request->all(), static::rulesForCreation($request))
                ->after(function ($validator) use ($request) {
                    static::afterValidation($request, $validator);
                    static::afterCreationValidation($request, $validator);
                });
    }

    /**
     * Get the validation rules for a resource creation request.
     *
     * @param AdminRequest $request
     * @return array
     */
    public static function rulesForCreation(AdminRequest $request)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->resolveCreationFields($request)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getCreationRules($request);
                    })->all());
    }

    /**
     * Get the creation validation rules for a specific field.
     *
     * @param AdminRequest $request
     * @param  string  $field
     * @return array
     */
    public static function creationRulesFor(AdminRequest $request, $field)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->availableFields($request)
                    ->where('attribute', $field)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getCreationRules($request);
                    })->all());
    }

    /**
     * Validate a resource update request.
     *
     * @param AdminRequest $request
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function validateForUpdate(AdminRequest $request)
    {
        static::validatorForUpdate($request)->validate();
    }

    /**
     * Create a validator instance for a resource update request.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Validation\Validator
     */
    public static function validatorForUpdate(AdminRequest $request)
    {
        return Validator::make($request->all(), static::rulesForUpdate($request))
                ->after(function ($validator) use ($request) {
                    static::afterValidation($request, $validator);
                    static::afterUpdateValidation($request, $validator);
                });
    }

    /**
     * Get the validation rules for a resource update request.
     *
     * @param AdminRequest $request
     * @return array
     */
    public static function rulesForUpdate(AdminRequest $request)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->resolveUpdateFields($request)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getUpdateRules($request);
                    })->all());
    }

    /**
     * Get the update validation rules for a specific field.
     *
     * @param AdminRequest $request
     * @param  string  $field
     * @return array
     */
    public static function updateRulesFor(AdminRequest $request, $field)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->availableFields($request)
                    ->where('attribute', $field)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getUpdateRules($request);
                    })->all());
    }

    /**
     * Validate a resource attachment request.
     *
     * @param AdminRequest $request
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function validateForAttachment(AdminRequest $request)
    {
        static::validatorForAttachment($request)->validate();
    }

    /**
     * Create a validator instance for a resource attachment request.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Validation\Validator
     */
    public static function validatorForAttachment(AdminRequest $request)
    {
        return Validator::make($request->all(), static::rulesForAttachment($request));
    }

    /**
     * Get the validation rules for a resource attachment request.
     *
     * @param AdminRequest $request
     * @return array
     */
    public static function rulesForAttachment(AdminRequest $request)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->creationPivotFields($request, $request->relatedResource)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getCreationRules($request);
                    })->all());
    }

    /**
     * Validate a resource attachment update request.
     *
     * @param AdminRequest $request
     * @return void
     */
    public static function validateForAttachmentUpdate(AdminRequest $request)
    {
        static::validatorForAttachmentUpdate($request)->validate();
    }

    /**
     * Create a validator instance for a resource attachment update request.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Validation\Validator
     */
    public static function validatorForAttachmentUpdate(AdminRequest $request)
    {
        return Validator::make($request->all(), static::rulesForAttachmentUpdate($request));
    }

    /**
     * Get the validation rules for a resource attachment update request.
     *
     * @param AdminRequest $request
     * @return array
     */
    public static function rulesForAttachmentUpdate(AdminRequest $request)
    {
        return static::formatRules($request, (new static(static::newModel()))
                    ->updatePivotFields($request, $request->relatedResource)
                    ->mapWithKeys(function ($field) use ($request) {
                        return $field->getUpdateRules($request);
                    })->all());
    }

    /**
     * Perform any final formatting of the given validation rules.
     *
     * @param AdminRequest $request
     * @param  array  $rules
     * @return array
     */
    protected static function formatRules(AdminRequest $request, array $rules)
    {
        $replacements = array_filter([
            '{{resourceId}}' => $request->resourceId,
        ]);

        if (empty($replacements)) {
            return $rules;
        }

        return collect($rules)->map(function ($rules) use ($replacements) {
            return collect($rules)->map(function ($rule) use ($replacements) {
                return is_string($rule)
                            ? str_replace(array_keys($replacements), array_values($replacements), $rule)
                            : $rule;
            })->all();
        })->all();
    }

    /**
     * Get the validation attribute for a specific field.
     *
     * @param AdminRequest $request
     * @param  string  $field
     * @return string
     */
    public static function validationAttributeFor(AdminRequest $request, $field)
    {
        return (new static(static::newModel()))
                    ->availableFields($request)
                    ->firstWhere('resourceName', $field)
                    ->getValidationAttribute($request);
    }

    /**
     * Handle any post-validation processing.
     *
     * @param AdminRequest $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected static function afterValidation(AdminRequest $request, $validator)
    {
        //
    }

    /**
     * Handle any post-creation validation processing.
     *
     * @param AdminRequest $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected static function afterCreationValidation(AdminRequest $request, $validator)
    {
        //
    }

    /**
     * Handle any post-update validation processing.
     *
     * @param AdminRequest $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected static function afterUpdateValidation(AdminRequest $request, $validator)
    {
        //
    }
}
