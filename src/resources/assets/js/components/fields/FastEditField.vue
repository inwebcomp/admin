<template>
    <td @click.ctrl="open" class="cursor-edit">
        <slot v-if="!opened"></slot>
        <div v-if="opened">
            <component @blur="hide" @select="hide" ref="component" :is="component" v-model="value" v-bind="props"></component>
        </div>
    </td>
</template>

<script>
    export default {
        name: "FastEditField",

        props: {
            field: {
                type: Object,
                default: null,
            },
            resourceName: {},
            resourceId: {},
        },

        data() {
            return {
                opened: false,
                component: this.field.fastEditComponent,
                value: null,
                originalValue: null,
                props: null,
            }
        },

        methods: {
            open() {
                App.api.request({
                    url: this.resourceName + '/' + this.resourceId + '/fast-edit/' + this.field.fastEdit,
                }).then(({value, component, props}) => {
                    this.bindHide()
                    this.opened = true
                    this.value = value
                    this.originalValue = value
                    this.props = props

                    this.$nextTick(() => {
                        if (this.$refs.component.$refs.input)
                            this.$refs.component.$refs.input.focus()
                        if (this.$refs.component.$refs.value)
                            this.$refs.component.$refs.value.focus()
                    })
                })
            },

            hide() {
                if (this.originalValue != this.value) {
                    this.save()
                } else {
                    this.opened = false
                    this.unbindHide()
                }
            },

            save() {
                App.api.request({
                    method: 'POST',
                    url: this.resourceName + '/' + this.resourceId + '/fast-edit/' + this.field.fastEdit,
                    data: {
                        value: this.value,
                    }
                }).then(({value}) => {
                    this.field.value = value
                }).finally(() => {
                    this.opened = false
                    this.unbindHide()
                })
            },

            bindHide() {
                let el = this.$el
                el.event = (event) => {
                    if (!(el === event.target || el.contains(event.target))) {
                        this.hide(event)
                    }
                }
                document.body.addEventListener('click', el.event, true)
            },

            unbindHide() {
                document.body.removeEventListener('click', this.$el.event, true)
            },
        },
    }
</script>