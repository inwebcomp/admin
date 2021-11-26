<template>
    <div class="icon-field" :class="'bg-' + field.background" :style="{color: field.color || 'inherit', background: (field.background && field.background.charAt(0) == '#' ? field.background : null) || null}" :title="field.title" v-if="field.value !== null && field.value !== false">
        <i v-if="! field.img && ! field.html" :class="field.icon"></i>
        <img v-if="field.img && ! field.html" :src="field.icon" height="26" width="26" :alt="field.title">
        <span class="icon-field-container" v-if="field.html" v-html="field.icon"></span>
    </div>
</template>

<script>
    export default {
        props: ['field'],
    }
</script>

<style lang="scss" scoped>
    .icon-field {
        display: inline-block;
        color: #222;
        height: 26px;
        line-height: 26px;
        width: 26px;
        text-align: center;
        border-radius: 13px;
    }

    .icon-field-container {
        height: 26px;
        width: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
