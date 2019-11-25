<template>
    <b-row align-v="center" class="mb-3" v-if="totalRows > 0">
        <b-col>
            <b-pagination
                size="sm"
                v-model="currentPage"
                :total-rows="totalRows"
                :per-page="perPage"
                :aria-controls="forId"
                class="mb-0"
            ></b-pagination>
        </b-col>
        <b-col sm="auto" class="text-right">
            <small>{{ ((currentPage - 1) * perPage) + 1 }} - {{ Math.min(currentPage * perPage, totalRows) }} / {{ totalRows }}</small>
        </b-col>
    </b-row>
</template>

<script>
export default {
    props: {
        forId: {
            type: String,
            required: true
        },
        totalRows: {
            type: Number,
            required: true,
        },
        value: {
            type: Number,
            required: true,
        },
        perPage: {
            type: Number,
            required: true,
        },
    },
    computed: {
        currentPage: {
            get() { return this.value },
            set(currentPage) {this.$emit('input', currentPage)}
        }
    }
}
</script>