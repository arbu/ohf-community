import { EventBus } from '@/event-bus'
export default {
    data() {
        return {
            open: false
        }
    },
    watch: {
        open(val, oldVal) {
            if (val && !oldVal) {
                EventBus.$emit('menu-opened');
            }
        }
    },
    mounted() {
        EventBus.$on('overlay-clicked', () => {
            this.open = false
        });
    },
    methods: {
        toggleMenu() {
            this.open = ! this.open
        }
    }
}