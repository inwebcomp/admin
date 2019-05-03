<template>
    <default-field :field="field" :errors="errors" :inline="inline" fullWidthContent>
        <template slot="field">
            <div class="tree" :id="field.attribute">
                <div class="flex">
                    <div class="tree__selected form__group__input" @click="opened = ! opened">
                        {{ title }}
                    </div>
                    <a v-if="opened" class="tree__toggle ml-2" @click="opened = false">
                        {{ __('Скрыть') }}
                    </a>
                </div>

                <ul v-if="opened" class="tree__list mt-2">
                    <li class="tree__item" @click="root">
                        <div class="tree__item__line">
                            <i class="far fa-folder-open mr-2"></i>
                            {{ __('Корень') }}
                        </div>
                    </li>

                    <li v-for="(item, $i) of tree" :key="$i"
                        class="tree__item" :class="getTreeClasses(item)"
                        @click="select(item)">
                        <div class="tree__item__line">
                            <i class="far mr-2" :class="getIconClasses(item)"></i>
                            {{ item.title }}
                        </div>
                    </li>
                </ul>
            </div>
        </template>
    </default-field>
</template>

<script>
    import HandlesValidationErrors from "~mixins/HandlesValidationErrors"
    import FormField from "~mixins/FormField"

    export default {
        mixins: [HandlesValidationErrors, FormField],

        data() {
            return {
                tree: [],
                item: null,
                opened: false,
            }
        },

        computed: {
            defaultAttributes() {
                return {
                    type: this.field.type || 'text',
                    min: this.field.min,
                    max: this.field.max,
                    step: this.field.step,
                    pattern: this.field.pattern,
                    placeholder: this.field.placeholder || this.field.name,
                    class: this.errorClasses,
                }
            },

            extraAttributes() {
                const attrs = this.field.extraAttributes

                return {
                    ...this.defaultAttributes,
                    ...attrs,
                }
            },

            title() {
                return this.item ? this.item.title : this.__('Выбрать')
            }
        },

        created() {
            this.fetch(this.field.value)
        },

        methods: {
            getTreeClasses(item) {
                let classes = {}
                classes['tree__item--level-' + item.level] = true
                classes['tree__item--active'] = this.item ? item.id == this.item.id : false

                return classes
            },

            getIconClasses(item) {
                let classes = {}
                classes['fa-folder' + (item.ancestor ? '-open' : '')] = true;
                classes['tree__item__arrow--hidden'] = item.isLeaf;

                return classes
            },

            root() {
                this.handleChange(null)
                this.fetch()
            },

            select(item) {
                this.handleChange(item.id)
                this.item = item
                this.fetch(item.id)
                this.$emit('input', item.id)
            },

            fetch(id = null) {
                App.api.request({
                    url: 'field/tree-field/tree/' + this.resourceName + '/' + this.resourceId,
                    params: {
                        id,
                        model: this.field.model
                    }
                }).then(({tree, item}) => {
                    this.tree = tree
                    this.item = item
                })
            }
        }
    }
</script>

