<template>
    <a
        :href="url"
        class="btn btn-primary btn-lg d-md-none floating-action-button"
    >
        <font-awesome-icon :icon="icon" />
    </a>
</template>

<script>
export default {
    props: {
        url: {
            type: String,
            required: true
        },
        icon: {
            type: String,
            required: true
        }
    }
}
</script>