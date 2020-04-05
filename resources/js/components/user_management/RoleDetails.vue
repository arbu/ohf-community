<template>
    <div>
        <!-- Edit -->
        <template v-if="edit">

            <role-editor
                :role="role"
                @cancel="edit = false"
                @save="role.name = $event.name; edit = false"
            />

        </template>

        <!-- User search -->
        <template v-else-if="showUserSearch">

            <h2 class="display-4">{{ $t('app.add_user_to_role_name', { name: role.name }) }}</h2>

            <!-- Component to add users -->
            <user-add-component
                :api-url="userListApiUrl"
                :unavailable-users="users.map(u => u.id)"
                class="mt-3"
                @select="addUser($event); showUserSearch = false"
            />

            <b-button
                variant="secondary"
                key="cancel-add-users"
                @click="showUserSearch = false"
            >
                <font-awesome-icon icon="times-circle"/>
                {{ $t('app.cancel') }}
            </b-button>

        </template>

        <!-- Administrator search -->
        <template v-else-if="showAdministratorsSearch">

            <h2 class="display-4">{{ $t('app.add_administrator_to_role_name', { name: role.name }) }}</h2>

            <!-- Component to add administrators -->
            <user-add-component
                :api-url="userListApiUrl"
                :unavailable-users="administrators.map(u => u.id)"
                class="mt-3"
                @select="addAdministrator($event); showAdministratorsSearch = false"
            />

            <b-button
                variant="secondary"
                key="cancel-add-users"
                @click="showAdministratorsSearch = false"
            >
                <font-awesome-icon icon="times-circle"/>
                {{ $t('app.cancel') }}
            </b-button>

        </template>

        <!-- Overview -->
        <template v-else>

            <!-- Error message -->
            <error-alert
                v-if="error"
                :message="error"
            />

            <!-- Title -->
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="display-4">{{ $t('app.role_name', { name: role.name}) }}</h2>
                <b-button
                    variant="primary"
                    @click="edit = true"
                >
                    <font-awesome-icon icon="edit"/>
                    {{ $t('app.edit') }}
                </b-button>
            </div>
            <p>
                {{ $t('app.created') }}: {{ createdDateString }}
            </p>

            <div class="row">
                <div class="col">

                    <!-- List users -->
                    <role-details-user-list
                        :users="users"
                        :loaded="usersLoaded"
                        :title="$t('app.users')"
                        @add="showUserSearch = true"
                        @remove="removeUser"
                    />

                </div>
                <div class="col">

                    <!-- List administrators -->
                    <role-details-user-list
                        :users="administrators"
                        :loaded="administratorsLoaded"
                        :title="$t('app.role_administrators')"
                        @add="showAdministratorsSearch = true"
                        @remove="removeAdministrator"
                    />

                </div>
            </div>

            <b-button
                variant="secondary"
                key="show-add-users"
                @click="$emit('close')"
            >
                <font-awesome-icon icon="times-circle"/>
                {{ $t('app.close') }}
            </b-button>

        </template>


    </div>
</template>

<script>
import { getAjaxErrorMessage, showSnackbar } from '@/utils'
import ErrorAlert from '@/components/alerts/ErrorAlert'
import RoleDetailsUserList from '@/components/user_management/RoleDetailsUserList'
import UserAddComponent from '@/components/user_management/UserAddComponent'
import RoleEditor from '@/components/user_management/RoleEditor'
import moment from 'moment'
import { BButton } from 'bootstrap-vue'
export default {
    components: {
        BButton,
        ErrorAlert,
        RoleDetailsUserList,
        UserAddComponent,
        RoleEditor
    },
    props: {
        role: {
            required: true,
            type: Object
        },
        userListApiUrl: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            users: [],
            error: null,
            showUserSearch: false,
            usersLoaded: false,
            showAdministratorsSearch: false,
            administrators: [],
            administratorsLoaded: false,
            edit: false
        }
    },
    computed: {
        createdDateString() {
            const date = moment(this.role.created_at)
            return `${date.format(moment.HTML5_FMT.DATE)} (${date.fromNow()})`
        }
    },
    mounted() {
        this.loadUsers()
        this.loadAdministrators()
    },
    methods: {
        loadUsers() {
            axios.get(this.role.relationships.users.links.related)
                .then(res => {
                    this.error = null
                    this.usersLoaded = true
                    this.users = res.data.data
                })
                .catch(err => this.error = getAjaxErrorMessage(err))
        },
        addUser(user) {
            axios.post(this.role.relationships.users.links.self, {
                    id: [
                        user.id
                    ]
                })
                .then(res => {
                    this.error = null
                    this.users = this.users.concat([user]).sort((a, b) => a.name.localeCompare(b.name));
                    showSnackbar(res.data.message)
                })
                .catch(err => this.error = getAjaxErrorMessage(err))
        },
        removeUser(user) {
            axios.delete(this.role.relationships.users.links.self, {
                    data: {
                        id: [
                            user.id
                        ]
                    }
                })
                .then(res => {
                    this.error = null
                    this.users.splice(this.users.indexOf(user), 1)
                    showSnackbar(res.data.message)
                })
                .catch(err => this.error = getAjaxErrorMessage(err))
        },
        loadAdministrators() {
            axios.get(this.role.relationships.administrators.links.related)
                .then(res => {
                    this.error = null
                    this.administratorsLoaded = true
                    this.administrators = res.data.data
                })
                .catch(err => this.error = getAjaxErrorMessage(err))
        },
        addAdministrator(administrator) {
            axios.post(this.role.relationships.administrators.links.self, {
                    id: [
                        administrator.id
                    ]
                })
                .then(res => {
                    this.error = null
                    this.administrators = this.administrators.concat([administrator]).sort((a, b) => a.name.localeCompare(b.name));
                    showSnackbar(res.data.message)
                })
                .catch(err => this.error = getAjaxErrorMessage(err))
        },
        removeAdministrator(administrator) {
            axios.delete(this.role.relationships.administrators.links.self, {
                    data: {
                        id: [
                            administrator.id
                        ]
                    }
                })
                .then(res => {
                    this.error = null
                    this.administrators.splice(this.administrators.indexOf(administrator), 1)
                    showSnackbar(res.data.message)
                })
                .catch(err => this.error = getAjaxErrorMessage(err))
        },
    }
}
</script>