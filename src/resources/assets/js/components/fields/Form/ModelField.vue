<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other">
        <template slot="field">
            <app-select :id="field.attribute"
                        :value="value"
                        :options="options"
                        @input="$emit('input', $event)"
                        v-bind="extraAttributes"
                        @search="search"
                        :class="errorClasses()"
                        ref="select">
                <template v-slot="{ options }">
                    <ul class="dropdown__values search__values">
                        <li v-for="(option, $i) in options" :key="$i" class="dropdown__option"
                            @click="select(option.value)">
                        <span v-if="option.image" class="dropdown__option__image"
                              :style="{ 'background-image': 'url(' + option.image + ')' }"></span>

                            <div class="dropdown__option__text search__option__text">
                                <div class="search__option__title">{{ option.title }}</div>
                                <div class="search__option__subtitle">{{ option.subTitle }}</div>
                                <div class="search__option__id">ID: {{ option.resourceId }}<i v-if="! option.visibility"
                                                                                              class="fas fa-eye-slash  text-grey ml-2"></i>
                                </div>
                            </div>
                        </li>
                    </ul>
                </template>
            </app-select>
        </template>
    </default-field>
</template>

<script>
    import HandlesValidationErrors from "~mixins/HandlesValidationErrors"
    import FormField from "~mixins/FormField"

    export default {
        mixins: [FormField, HandlesValidationErrors],

        computed: {
            defaultAttributes() {
                return {}
            },

            extraAttributes() {
                const attrs = this.field.extraAttributes

                return {
                    ...this.defaultAttributes,
                    ...attrs,
                }
            },
        },

        data() {
            return {
                options: []
            }
        },

        mounted() {
            if (this.value && ! this.options.length) {
                this.search(this.value)
            }
        },

        methods: {
            search(search) {
                App.api.request({
                    url: 'field/model-field/' + this.field.resource + '/search',
                    params: {
                        search
                    }
                }).then((data) => {
                    this.options = data
                })
            },

            select(value) {
                // this.handleChange(value)
                this.$refs.select.select(value)
            }
        },
    }
</script>

