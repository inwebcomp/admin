<template>
    <div>
        <span v-if="!field.link">{{ field.value }}</span>
        <router-link v-if="field.link" :to="{ path: field.link, query: $route.query }" class="data-table__value__link" :class="field.linkClasses">
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
