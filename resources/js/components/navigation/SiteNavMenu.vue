 <template>
    <div class="position-relative d-inline-block">
        <button class="context-nav-toggle btn btn-link text-light px-3">
            <font-awesome-icon icon="ellipsis-v" />
        </button>
        <ul class="context-nav">
            <site-nav-menu-item
                v-for="(item, idx) in items"
                :key="idx"
                :item="item"
            />
        </ul>
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
        }
    }
}
</script>