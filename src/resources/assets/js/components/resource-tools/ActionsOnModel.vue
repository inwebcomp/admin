<template>
    <div>
        <div class="flex items-center mb-4">
            <app-button @click.native="fetch" small class="mr-4">{{ __('Обновить') }}</app-button>
            <label class="flex items-center cursor-pointer">
                <switch-input class="mr-2" v-model="watch"/>
                {{ __('Автообновление через :n сек.', { n: this.timeout / 1000 }) }}
            </label>
        </div>

        <editable-list class="mb-4"
                       v-model="actions"
                       :headers="[__('Пользователь'), __('Действие'), __('Время'), __('Изменения')]">

            <template slot-scope="{ item }">
                <td class="py-4 px-6 border-b border-grey-light">{{ item.user.login }}</td>
                <td class="py-4 px-6 border-b border-grey-light">
                    <div class="flex items-center">
                        <span>{{ item.name }}</span>
                        <span :title="statusTitle(item.status)" class="status-field__icon ml-2" :class="'bg-' + statusColor(item.status)"></span>
                    </div>
                    <a v-if="item.exception" @click="(item.showError = ! item.showError) && (watch = false)">{{ item.showError ? __('Скрыть ошибку') : __('Показать ошибку') }}</a>
                    <div v-if="item.exception && item.showError"
                         class="bg-black shadow text-sm text-white p-2 mt-1 overflow-auto"
                         style="max-width: 300px; max-height: 500px">
                        {{ item.exception }}
                    </div>
                </td>
                <td class="py-4 px-6 border-b border-grey-light">{{ item.updated_at }}</td>
                <td class="py-4 px-6 border-b border-grey-light">
                    <div v-for="(value, field) in item.changes" class="border-b border-grey-light">
                        <span class="text-sm font-bold">{{ field }}</span><br>
                        <span>{{ item.original ? item.original[field] : '' }} <i class="fal fa-long-arrow-right"></i> {{ value }}</span>
                    </div>
                </td>
            </template>
        </editable-list>
    </div>
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
                actions: [],
                watch: false,
                watchIntervals: [null, null],
                watchTimeout: 3000,
                timeout: 3000,
            }
        },

        created() {
            this.fetch()

            App.$on('actionExecuted', this.fetch)

            App.$on('editFormTabChange', (tab) => {
                if (tab.uid == 'actions-on-model-tool') {
                    this.watch = true
                    this.fetch()
                } else {
                    this.watch = false
                }
            })
        },

        watch: {
            watch(value) {
                if (value) {
                    this.watchIntervals[0] = setInterval(() => {
                        if (this.timeout > 0)
                            this.timeout -= 1000
                    }, 1000)

                    this.watchIntervals[1] = setInterval(this.fetch, this.watchTimeout)
                } else {
                    clearInterval(this.watchIntervals[0])
                    clearInterval(this.watchIntervals[1])
                }
            }
        },

        methods: {
            fetch() {
                this.timeout = this.watchTimeout

                App.api.request({
                    url: 'resource-tool/actions-on-model-tool/' + this.resourceName + '/' + this.resourceId,
                }).then((data) => {
                    this.actions = data.map(item => {
                        item.showError = false
                        return item
                    })
                })
            },

            statusColor(status) {
                if (status == 'finished')
                    return 'green';
                if (status == 'waiting')
                    return 'blue';
                if (status == 'running')
                    return 'warning';
                if (status == 'failed')
                    return 'danger';

                return false
            },

            statusTitle(status) {
                if (status == 'finished')
                    return this.__('Завершено');
                if (status == 'waiting')
                    return this.__('Запускается');
                if (status == 'running')
                    return this.__('Выполняется');
                if (status == 'failed')
                    return this.__('Ошибка');

                return false
            },
        },
    }
</script>

<style lang="scss" scoped>
    .status-field {
        &__icon {
            display: inline-block;
            color: #222;
            height: 16px;
            line-height: 16px;
            width: 16px;
            border-radius: 8px;
        }
    }
</style>