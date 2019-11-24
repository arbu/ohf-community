<template>
    <i :class="{ fa: true, [`fa-${name}`]: true, 'fa-spin': spin }"></i>
</template>
<script>
export default {
    props: {
        name: {
            type: String,
            required: true
        },
        spin: {
            type: Boolean,
            required: false,
            default: false
        }
    }
}
</script>