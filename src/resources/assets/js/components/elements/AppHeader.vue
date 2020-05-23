<template>
    <header class="header">
        <div class="header__sitename">
            <a target="_blank" href="/" class="header__sitename__link">{{ sitename }}</a>
        </div>

        <search/>

        <div class="header__language">

        </div>

        <!-- Settings -->
        <settings-menu />

        <div class="header__user">{{ user }}</div>

        <div class="header__exit" @click="logout"><i class="fas fa-sign-out-alt"></i></div>
    </header>
</template>

<script>
    export default {
        name: "app-header",

        data() {
            return {
                sitename: App.config.sitename
            }
        },

        computed: {
            user() {
                return this.$store.state.user.info ? this.$store.state.user.info.login : null
            }
        },

        methods: {
            logout() {
                App.api.action({
                    method: 'POST',
                    url: 'logout',
                }).then(() => {
                    App.$emit('loggedOut')

                    this.$toasted.show(
                        this.__('You have successfully logged out!'),
                        {type: 'success'}
                    )

                    this.$router.push({ name: 'login' })
                })
            },
        },
    }
</script>
