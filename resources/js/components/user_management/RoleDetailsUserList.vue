<template>
    <b-card
        no-body
        class="mb-4"
    >
        <template v-slot:header>
            <div class="d-flex justify-content-between align-items-center">
                <span>{{ cardHeader }}</span>
                <b-button
                    variant="success"
                    size="sm"
                    @click="$emit('add')"
                >
                    <font-awesome-icon icon="plus-circle"/>
                </b-button>
            </div>
        </template>
        <b-list-group
            flush
        >
            <b-list-group-item
                v-if="! loaded"
                class="text-center text-muted"
            >
                <font-awesome-icon
                    icon="spinner"
                    spin
                />
                {{ $t('app.loading') }}
            </b-list-group-item>
            <template v-else-if="users.length > 0">
                <b-list-group-item
                    v-for="user in users"
                    :key="user.id"
                    class="d-flex justify-content-between align-items-center"
                >
                    <div>
                        {{ user.name }}<br>
                        <small>{{ user.email }}</small>
                    </div>
                    <b-button
                        variant="danger"
                        size="sm"
                        @click="removeUser(user)"
                    >
                        <font-awesome-icon icon="minus-circle"/>
                    </b-button>
                </b-list-group-item>
            </template>
            <b-list-group-item
                v-else
                class="text-center"
            >
                <em>{{ $t('app.no_users_assigned') }}</em>
            </b-list-group-item>
        </b-list-group>
    </b-card>
</template>

<script>
import { BCard, BListGroup, BListGroupItem, BButton } from 'bootstrap-vue'
export default {
    components: {
        BCard,
        BListGroup,
        BListGroupItem,
        BButton
    },
    props: {
        users: {
            required: true,
            type: Array
        },
        title: {
            type: String,
            required: true
        },
        loaded: Boolean
    },
    computed: {
        cardHeader() {
            let str = this.title
            if (this.loaded) {
                str += ` (${this.users.length})`
            }
            return str
        }
    },
    methods: {
        removeUser(user) {
            if (confirm(this.$t('app.really_remove_user', { name: user.name }))) {
                this.$emit('remove', user)
            }
        }
    }
}
</script>