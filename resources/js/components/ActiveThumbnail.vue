<template>
    <a :href="href" :data-lity="lightbox">
        <thumbnail :href="href" :size="size"></thumbnail>
    </a>
</template>
<script>
    import Thumbnail from './Thumbnail'
    export default {
        components: {
            Thumbnail,
        },
        extends: Thumbnail,
        props: {
            lightbox: {
                type: Boolean,
                required: false,
                default: true
            }
        }
    }
</script>