<template>
    <b-list-group-item
        :button="available"
        class="d-flex justify-content-between align-items-center"
        @click="available ? $emit('select') : null"
    >
        <span :class="{ 'text-black-50': ! available }">
            <text-highlight :queries="highlightTerms">{{ user.name }}</text-highlight><br>
            <small><text-highlight :queries="highlightTerms">{{ user.email }}</text-highlight></small>
        </span>
        <small v-if="! available && unavailableLabel">
            <em>{{ unavailableLabel }}</em>
        </small>
    </b-list-group-item>
</template>

<script>
import { BListGroupItem } from 'bootstrap-vue'
import TextHighlight from 'vue-text-highlight'
export default {
    components: {
        BListGroupItem,
        TextHighlight
    },
    props: {
        user: {
            type: Object,
            required: true
        },
        available: Boolean,
        highlight: {
            type: String,
            required: false,
            default: ''
        },
        unavailableLabel: {
            type: String,
            required: false,
            default: null
        }
    },
    computed: {
        highlightTerms() {
            return [this.highlight]
        }
    }
}
</script>