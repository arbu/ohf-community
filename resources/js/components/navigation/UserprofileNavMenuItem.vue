<template>
    <li>
        <a
            :href="url"
            class="btn btn-dark btn-block"
            @click="$emit('click')"
        >
            <font-awesome-icon :icon="icon" class="mr-1"/>
            {{ caption }}
        </a>
    </li>
</template>

<script>
export default {
    props: {
        caption: {
            type: String,
            required: true
        },
        icon: {
            type: String,
            required: true
        },
        url: {
            type: String,
            required: true
        }
    }
}
</script>