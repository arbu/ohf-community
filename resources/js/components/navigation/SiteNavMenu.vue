 <template>
    <div class="position-relative d-inline-block">
        <button
            class="btn btn-link text-light px-3"
            @click="toggleMenu"
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
export default {
    components: {
        SiteNavMenuItem
    },
    props: {
        items: {
            type: Object,
            required: true
        },
        open: Boolean
    },
    methods: {
        toggleMenu() {
            this.$emit('toggle')
        }
    }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity .2s;
}

.fade-enter, .fade-leave-to {
    opacity: 0;
}
</style>