<template>
    <editable-list class="mb-4"
                   v-model="actions"
                   :headers="[__('Пользователь'), __('Действие'), __('Время'), __('Изменения')]">

        <template slot-scope="{ item }">
            <td class="py-4 px-6 border-b border-grey-light">{{ item.user.login }}</td>
            <td class="py-4 px-6 border-b border-grey-light">{{ item.name }}</td>
            <td class="py-4 px-6 border-b border-grey-light">{{ item.updated_at }}</td>
            <td class="py-4 px-6 border-b border-grey-light">
                <div v-for="(value, field) in item.changes" class="border-b border-grey-light">
                    <span class="text-sm font-bold">{{ field }}</span><br>
                    <span>{{ item.original ? item.original[field] : '' }} <i class="fal fa-long-arrow-right"></i> {{ value }}</span>
                </div>
            </td>
        </template>
    </editable-list>
</template>

<script>
    export default {
        name: 'actions-on-model-tool',

        props: {
            resourceName: {},
            resourceId: {},
        },

        data() {
            return {
                actions: []
            }
        },

        created() {
            this.fetch()
        },

        methods: {
            fetch() {
                App.api.request({
                    url: 'resource-tool/actions-on-model-tool/' + this.resourceName + '/' + this.resourceId,
                }).then((data) => {
                    this.actions = data
                })
            },
        },
    }
</script>
