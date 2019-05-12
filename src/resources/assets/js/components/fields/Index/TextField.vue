<template>
    <div>
        <span v-if="!field.link">{{ field.value }}</span>
        <router-link v-if="field.link" :to="field.link" class="data-table__value__link whitespace-no-wrap">
            {{ field.value }}
        </router-link>
        <div v-if="field.subtitle" v-html="field.subtitle"></div>
    </div>
</template>

<script>
    export default {
        props: ['field'],
    }
</script>
