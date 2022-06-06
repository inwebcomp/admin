<template>
    <div class="form__group__input-group">
        <slot>
            <textarea
                    ref="input"
                    class="w-full form__group__input form__group__input--textarea h-auto"
                    :class="{'form__group__input--h-small': small}"
                    :value="value"
                    :placeholder="placeholder"
                    :type="type"
                    :step="step"
                    :tabindex="tabindex"
                    @input="$emit('input', $event.target.value)"
                    @blur="$emit('blur', $event)"
                    @focus="$emit('focus', $event)"
                    @change="$emit('change', $event)"
                    @keypress.enter="$emit('enter', $event)"
                    @keypress.esc="$emit('esc', $event)"
            />

            <div class="form__group__erase" :class="{'form__group__erase--h-small': small}" v-show="eraseIcon && value != ''" @click="$emit('input', '') && $emit('erase', '')">
                <i class="fal fa-times"></i>
            </div>
        </slot>
    </div>
</template>

<script>
    export default {
        name: 'textarea-input',

        props: {
            value: {},
            type: {
                default: 'text',
            },
            eraseIcon: {
                type: Boolean,
                default: false,
            },
            placeholder: {},
            tabindex: {},
            step: {},
            small: {
                type: Boolean,
                default: false,
            },
        },
    }
</script>

