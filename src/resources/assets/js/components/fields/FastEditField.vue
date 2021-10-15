<template>
    <td @click="open" class="data-table__value--fast-edit cursor-edit">
        <slot v-if="!opened"></slot>
        <div v-if="opened">
            <component @change="hide" ref="component" :is="component" v-model="value" v-bind="props"></component>
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
                if (this.opened)
                    return

                console.log('open')

                App.api.request({
                    url: this.resourceName + '/' + this.resourceId + '/fast-edit/' + this.field.fastEdit,
                }).then(({value, component, props}) => {
                    this.bindHide()
                    this.opened = true
                    this.value = value
                    this.originalValue = value
                    this.props = props

                    this.$nextTick(() => {
                        if (typeof this.$refs.component.focus == 'function')
                            this.$refs.component.focus()
                        else if (this.$refs.component.$refs.input)
                            this.$refs.component.$refs.input.focus()
                    })
                })
            },

            hide() {
                if (this.originalValue != this.value) {
                    this.save()
                } else {
                    setTimeout(() => {
                        this.opened = false
                        this.unbindHide()
                    }, 100)
                }
            },

            save() {
                return App.api.request({
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