<template>
    <div>
        <i v-if="field.value" class="far fa-check"></i>
    </div>
</template>

<script>
    export default {
        props: ['field'],
    }
</script>
