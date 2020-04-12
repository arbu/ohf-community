<template>
    <li
        class="nav-item"
    >
        <a
            class="nav-link"
            :class="{active: item.active}"
            :href="item.url"
        >
            <font-awesome-icon :icon="item.icon" :title="item.icon" />
            {{ item.caption }}
            <span
                v-if="item.badge"
                class="badge badge-secondary ml-2"
            >
                {{ item.badge }}
            </span>
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