<template>
    <div>
        <div v-if="volunteer != null">
            Volunteer
        </div>
        <volunteer-list></volunteer-list>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                volunteer: null,
            }
        },
        mounted() {
            this.refresh();
        },
        methods: {

        }
    }
</script>