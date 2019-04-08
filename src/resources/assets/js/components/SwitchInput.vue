<template>
    <div class="switch">
        <label class="switch__label">
            <input class="switch__input" type="checkbox" :id="_uid" :checked="value" value="1" name="remember" @change="$emit('input', ! value)">
            <span class="switch__lever"><i class="switch__icon far fa-check"></i></span>
        </label>
    </div>

    <!--<div class="checkbox" :class="{ 'checkbox&#45;&#45;success': success }">-->
        <!--<input type="checkbox" :id="_uid" class="checkbox__input" :checked="value" value="1" @click.stop="$emit('input', ! value)">-->
        <!--<label :for="_uid" class="checkbox__label">-->
            <!--<i class="fas fa-check checkbox__checker"></i>-->
        <!--</label>-->
    <!--</div>-->
</template>

<script>
    export default {
        name: "switch-input",

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
