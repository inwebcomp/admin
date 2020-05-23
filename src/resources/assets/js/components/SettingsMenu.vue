<template>
    <dropdown>
        <dropdown-trigger
                slot-scope="{ toggle }"
                :handle-click="toggle"
        >
            <a class="header__settings"><i class="fas fa-cogs"></i></a>
        </dropdown-trigger>

        <dropdown-menu class="text-black p-4" slot="menu" direction="rtl"
                       slot-scope="{ toggle }">
            <div class="form__group form__group--inline w-full mb-0">
                <form-label>{{ __('Язык') }}</form-label>
                <app-select :options="languages" small :search="false" v-model="language"/>
            </div>
        </dropdown-menu>
    </dropdown>
</template>

<script>
    export default {
        name: "SettingsMenu",

        data() {
            return {
                language: App.config.language,
            }
        },

        computed: {
            languages() {
                return App.config.languages.map((language) => {
                    return {
                        value: language,
                        title: language.toUpperCase()
                    }
                })
            }
        },

        watch: {
            language(value) {
                App.api.request({
                    method: 'POST',
                    url: 'settings',
                    data: {
                        field: 'language',
                        value: value
                    }
                }).then((data) => { console.log(data)
                    if (data.message) {
                        this.$toasted.show(data.message + '. ' + this.__('Обновление страницы') + ' ...', {
                            iconPack: 'fontawesome',
                            icon: 'fa-hourglass-half',
                            duration: 1000 * 1000,
                            type: "success"
                        })

                        setTimeout(() => {
                            window.location.reload()
                        }, 1000)
                    }
                })
            }
        }
    }
</script>