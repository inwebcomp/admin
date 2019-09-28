<template>
    <div class="p-8">
        <heading class="mb-6">{{ title }}</heading>

        <card class="bg-90 flex flex-col items-center justify-center" style="min-height: 300px">
            <p class="text-white-50% text-lg">
                You can edit this tool's component at:
                <code
                    class="ml-1 border border-80 text-sm font-mono text-white bg-black rounded px-2 py-1"
                    >/admin-components/{{ class }}/resources/js/components/Tool.vue</code
                >
            </p>
        </card>
    </div>
</template>

<script>
export default {
    mounted() {
        //
    },
}
</script>

<style>
/* Scoped Styles */
</style>
