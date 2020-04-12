<template>
    <li>
        <a :href="item.url" class="btn btn-light btn-block">
            <font-awesome-icon :icon="item.icon" class="mr-1" />
            {{ item.caption }}
        </a>
    </li>
</template>

<script>
export default {
    props: {
        item: {
            type: Object,
            required: true
        }
    }
}
</script>