<template>
    <div class="editable-list bg-white shadow-md rounded">
        <table class="text-left w-full border-collapse">
            <slot name="head">
                <thead v-if="headers.length">
                    <tr>
                        <th v-for="(item, $i) of headers" :key="$i"
                            class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light" v-html="item">
                        </th>
                    </tr>
                </thead>
            </slot>
            <draggable :value="value"
                       @input="$emit('input', $event)"
                       tag="tbody"
                       v-bind="dragOptions"
                       @start="drag = true"
                       @end="dragEnd">

                    <tr v-for="(item, $i) of value" :key="$i" :class="{'hover:bg-grey-lighter': !drag}">
                        <slot :item="item" :index="$i">
                            <td class="py-4 px-6 border-b border-grey-light cursor-move handle text-grey w-1"><i class="icon icon--handle"></i></td>

                            <td class="py-4 px-6 border-b border-grey-light">{{ item.title }}</td>
                            <td class="py-4 px-6 w-1 border-b border-grey-light text-center cursor-pointer hover:text-accent">
                                <i class="far fa-edit text-black-50"></i>
                            </td>
                        </slot>
                    </tr>
            </draggable>
        </table>
        <template v-if="! value.length && ! add">
            <div class="py-4 px-6 border-b border-grey-light text-center">{{ __('Список пуст') }}</div>
        </template>
        <template v-if="add">
            <div class="py-4 px-6 border-b border-grey-light text-center cursor-pointer hover:bg-grey-lighter"
                 @click="$emit('add')">
                <i class="fal fa-plus mr-2"></i> {{ __('Добавить') }}
            </div>
        </template>
    </div>
</template>

<script>
    import Draggable from "vuedraggable"

    export default {
        name: "EditableList",

        components: {
            Draggable
        },

        data() {
            return {
                drag: false,
                dragOptions: {
                    disabled: ! this.sortable,
                    delay: 0,
                    touchStartThreshold: 0,
                    forceFallback: true,
                    animation: 150,
                    ghostClass: "ghost",
                    handle: ".handle",
                    dragClass: "sortable-drag",
                }
            }
        },

        props: {
            value: {
                type: Array,
                default: () => []
            },
            headers: {
                type: Array,
                default: () => []
            },
            add: {
                type: Boolean,
                default: false,
            },
            sortable: {
                type: Boolean,
                default: false,
            }
        },

        methods: {
            dragEnd() {
                this.drag = false

                this.$emit('sort')
            }
        },
    }
</script>