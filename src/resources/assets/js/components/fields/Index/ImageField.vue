<template>
    <div class="flex">
        <template v-if="typeof field.value == 'object'">
            <div class="mr-2" v-for="(image, $i) in field.value">
                <img v-if="image" class="m-auto block" :src="img(image)" />
                <div style="width: 42px" v-if="! image"></div>
            </div>
        </template>
        <template v-if="typeof field.value != 'object'">
            <img v-if="field.value" class="m-auto block" :src="img(field.value)" />
            <div style="width: 42px" v-if="! field.value"></div>
        </template>
    </div>
</template>

<script>
    export default {
        props: ['field'],
    }
</script>
