<template>
    <section class="login-block">
        <form class="form" id="login-block" @submit.prevent="submit">
            <h1>{{ __('Вход в систему') }}</h1>

            <form-text-field class="w-full" ref="firstField"
                             :field="{name: __('Логин / Email'), attribute: 'id'}"
                             :errors="validationErrors"
                             v-model="form.id" />

            <form-text-field class="w-full"
                             :field="{name: __('Пароль'), attribute: 'password', type: 'password'}"
                             :errors="validationErrors"
                             v-model="form.password" />

            <form-boolean-field class="w-full remember"
                                inline
                                :field="{name: __('Оставаться в системе'), attribute: 'remember', size: 'none'}"
                                :errors="validationErrors"
                                v-model="form.remember" />

            <app-button submit class="w-full mt-4" type="accent" :loading="loading">{{ __('Вход') }}</app-button>
        </form>
    </section>
</template>

<script>
    import {Errors, Form} from 'form-backend-validation'

    export default {
        name: "login",

        data() {
            return {
                loading: false,
                validationErrors: new Errors(),
                form: new Form({
                    id: '',
                    password: '',
                    remember: false,
                    redirect: null,
                })
            }
        },

        methods: {
            submit() {
                if (this.loading)
                    return

                this.loading = true

                App.api.action({
                    method: 'POST',
                    url: 'signin',
                    data: this.formData('POST')
                }).then(({user}) => {
                    this.loading = false

                    this.$store.commit('user/set', user)

                    App.$emit('signin', user)

                    this.$toasted.show(
                        this.__('Welcome!'),
                        {type: 'success'}
                    )

                    this.validationErrors = new Errors()
                }).catch(({data, status}) => {
                    this.loading = false

                    if (status == 422) {
                        this.validationErrors = new Errors(data.errors)
                    }
                })
            },

            formData(method) {
                return _.tap(new FormData(), formData => {
                    for (let [field, value] of Object.entries(this.form.data())) {
                        if (value === undefined || value === null)
                            value = ''

                        formData.append(field, value)
                    }

                    formData.append('_method', method)
                })
            },
        },
    }
</script>

<style lang="scss">
    .remember {
        .form__group__label {
            margin: 0.5rem;
            width: 100%;
        }
    }
</style>