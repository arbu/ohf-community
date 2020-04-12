<template>
    <div class="position-relative d-inline-block">
        <button
            class="btn btn-link text-light px-3"
            @click="toggleMenu()"
        >
            <font-awesome-icon icon="ellipsis-v" />
        </button>
        <transition name="fade">
            <ul
                v-if="open"
                class="context-nav"
            >
                <site-nav-menu-item
                    v-for="(item, idx) in items"
                    :key="idx"
                    :item="item"
                />
            </ul>
        </transition>
    </div>
</template>

<script>
import SiteNavMenuItem from './SiteNavMenuItem'
import overlayMenuMixin from './overlayMenuMixin'
export default {
    components: {
        SiteNavMenuItem
    },
    mixins: [
        overlayMenuMixin
    ],
    props: {
        items: {
            type: Object,
            required: true
        }
    }
}
</script>
