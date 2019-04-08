<template>
    <router-link :to="field.link" class="data-table__value__link whitespace-no-wrap">{{ field.value }}</router-link>
</template>

<script>
    export default {
        props: ['field'],
    }
</script>
