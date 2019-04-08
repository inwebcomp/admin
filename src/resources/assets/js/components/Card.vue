<template>
    <section class="card">
        <div class="card__header" v-if="caption">
            <h2>{{ caption }}</h2>
        </div>

        <slot/>
    </section>
</template>

<script>
    export default {
        props: {
            caption: {
                default: null,
                type: String
            }
        }
    }
</script>
