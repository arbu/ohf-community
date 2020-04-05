<template>
    <div>
        <!-- Error message -->
        <error-alert
            v-if="error"
            :message="error"
        />

        <!-- Input field -->
        <b-form-input
            v-model="value"
            type="search"
            debounce="400"
            :placeholder="$t('app.search_ellipsis')"
            class="mb-4"
            autofocus
            autocomplete="off"
        />

        <!-- User list -->
        <b-list-group
            v-if="users.length > 0"
            class="mb-4"
        >

            <!-- User item -->
            <user-select-list-group-item
                v-for="user in users"
                :key="user.id"
                :user="user"
                :available="isAvailable(user)"
                :highlight="highlight"
                :unavailable-label="$t('app.already_added')"
                @select="$emit('select', user)"
            />

            <!-- Refine results message -->
            <b-list-group-item v-if="total > users.length">
                <em>{{ $t('app.not_all_results_listed_please_refine_criteria') }}</em>
            </b-list-group-item>

        </b-list-group>

        <!-- No users found message -->
        <p
            v-else-if="searched && !error"
            class="text-center"
        >
            {{ $t('app.no_results_found_for_term', { term: value }) }}
        </p>

    </div>
</template>

<script>
import { BFormInput, BListGroup, BListGroupItem } from 'bootstrap-vue'
import ErrorAlert from '@/components/alerts/ErrorAlert'
import UserSelectListGroupItem from '@/components/user_management/UserSelectListGroupItem'
import { getAjaxErrorMessage } from '@/utils'
export default {
    components: {
        BFormInput,
        BListGroup,
        BListGroupItem,
        ErrorAlert,
        UserSelectListGroupItem
    },
    props: {
        apiUrl: {
            type: String,
            required: true
        },
        unavailableUsers: {
            type: Array,
            required: false,
            default: () => []
        }
    },
    data() {
        return {
            value: '',
            users: [],
            total: 0,
            searched: false,
            error: null,
            highlight: ''
        }
    },
    watch: {
        value(val, oldVal) {
            this.searched = false
            const filter = val.trim()
            if (filter.length > 0) {
                this.filterUsers(filter)
                    .finally(() => this.searched = true)
            } else {
                this.users = []
                this.total = 0
            }
        }
    },
    methods: {
        isAvailable(user) {
            return ! this.unavailableUsers.includes(user.id)
        },
        filterUsers(value) {
            return axios.get(`${this.apiUrl}?filter=${value}`)
                .then(res => {
                    this.error = null
                    this.users = res.data.data
                    this.total = res.data.meta.total
                    this.highlight = value
                })
                .catch(err => this.error = getAjaxErrorMessage(err))
        }
    }
}
</script>