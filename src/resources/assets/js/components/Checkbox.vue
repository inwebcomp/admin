<template>
    <div class="checkbox" :class="{ 'checkbox--success': success }">
        <input type="checkbox" :id="_uid" class="checkbox__input" :checked="value" value="1" @click.stop="$emit('input', ! value)">
        <label :for="_uid" class="checkbox__label">
            <i class="fas fa-check checkbox__checker"></i>
        </label>
    </div>
</template>

<script>
    export default {
        name: "checkbox",

        props: {
            value: {
                type: Boolean,
                default: false
            },
            success: {
                type: Boolean,
                default: false
            }
        }
    }
</script>
