<template>
    <div>
        <span v-if="!field.link" v-html="field.value"></span>
    </div>
</template>

<script>
    export default {
        props: ['field'],
    }
</script>
