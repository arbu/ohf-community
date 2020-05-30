<template>
    <svg
        viewBox="0 0 1 1"
        :width="size"
        :height="size"
        style="max-width: 100%; height: auto;"
    >
        <image
            :xlink:href="src"
            width="100%"
            height="100%"
            preserveAspectRatio="xMidYMid slice"
        />
    </svg>
</template>

<script>
export default {
    props: {
        src: {
            type: String,
            required: true
        },
        size: {
            type: Number,
            required: false,
            default: 200
        }
    }
}
</script>
