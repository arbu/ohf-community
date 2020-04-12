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

<style scoped>
.floating-action-button {
    position: absolute;
    bottom: 1.8em;
    right: 1.8em;
    border-radius: 30px;
    box-shadow: 0 6px 10px 0 rgba(0,0,0,0.14),0 1px 18px 0 rgba(0,0,0,0.12),0 3px 5px -1px rgba(0,0,0,0.2);
}

.floating-action-button-elevated {
    bottom: 4.5em;
}
</style>