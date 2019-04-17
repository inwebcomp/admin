<template>
    <div>
        <img v-if="field.value" class="m-auto block" :src="img(field.value)" />
        <div style="width: 42px" v-if="! field.value"></div>
    </div>
</template>

<script>
    export default {
        props: ['field'],
    }
</script>
