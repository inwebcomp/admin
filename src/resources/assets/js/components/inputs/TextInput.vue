<template>
    <div class="form__group__input-group">
        <slot>
            <input
                    ref="input"
                    class="w-full form__group__input"
                    :class="{'form__group__input--h-small': small}"
                    :value="value"
                    :placeholder="placeholder"
                    @input="$emit('input', $event.target.value)"
                    @blur="$emit('blur', $event)"
                    @focus="$emit('focus', $event)"
                    @change="$emit('change', $event)"
                    @keypress.enter="$emit('enter', $event)"
            />
        </slot>
    </div>
</template>

<script>
    export default {
        name: 'text-input',

        props: {
            value: {},
            placeholder: {},
            small: {
                type: Boolean,
                default: false,
            },
        },
    }
</script>

