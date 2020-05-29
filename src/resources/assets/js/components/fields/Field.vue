<template>
    <component
        :is="resolveComponentName(field)"
        :resource-name="resourceName"
        :resource-id="resourceId"
        :field="field"
        v-model="field.value"
        @input="$emit('input', $event)"
        :errors="errors"
        :inline="inline"
    />
</template>

<script>
export default {
    props: {
        resourceName: { type: String },
        resourceId: { type: String },
        resource: { type: Object },
        field: { type: Object },
        errors: { type: Object },
        withHeader: { type: Boolean, default: true },
        inline: { type: Boolean, default: false },
    },

    methods: {
        resolveComponentName(field) {
            return field.prefixComponent ? 'form-' + field.component : field.component
        },
    },

    computed: {
        classes() {
            let classes = []

            if (! this.panel || ! this.panel.inline) {
                classes.push('flex', 'flex-wrap', '-mx-2', 'form--flex')
            }

            return classes
        }
    },
}
</script>
