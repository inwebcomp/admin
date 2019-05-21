<template>
    <card class="card--form" :caption="withHeader ? panel.name : null">
        <div class="p-8" :class="classes">
            <component
                :key="index"
                v-for="(field, index) in panel.fields"
                :is="resolveComponentName(field)"
                :resource-name="resourceName"
                :resource-id="resourceId"
                :field="field"
                v-model="field.value"
                :errors="errors"
                :inline="panel.inline"
            />
        </div>
    </card>
</template>

<script>
export default {
    props: {
        resourceName: { type: String },
        resourceId: { type: String },
        resource: { type: Object },
        panel: { type: Object },
        errors: { type: Object },
        withHeader: { type: Boolean, default: true },
    },

    methods: {
        resolveComponentName(field) {
            return field.prefixComponent ? 'form-' + field.component : field.component
        },
    },

    computed: {
        classes() {
            let classes = []

            if (! this.panel.inline) {
                classes.push('flex', 'flex-wrap', '-mx-2', 'form--flex')
            }

            return classes
        }
    },
}
</script>
