<template>
    <div>
        <span v-if="!field.link">{{ field.value }}</span>
    </div>
</template>

<script>
    export default {
        props: ['field'],
    }
</script>
