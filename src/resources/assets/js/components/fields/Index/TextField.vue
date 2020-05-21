<template>
    <div>
        <div v-if="!field.link">
            <span v-if="!field.asHtml" v-text="field.value"></span>
            <span v-if="field.asHtml" v-html="field.value"></span>
        </div>
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
