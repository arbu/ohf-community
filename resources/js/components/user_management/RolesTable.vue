<template>
    <div>
        <h2 class="display-4">{{ $t('app.roles') }}</h2>

        <b-table
            ref="table"
            striped small hover responsive bordered
            primary-key="id"
            :busy.sync="isBusy"
            :items="itemProvider"
            :fields="fields"
            :api-url="apiUrl"
            :show-empty="initialized"
            :empty-text="$t('app.no_roles_found')"
        >
            <!-- Busy state -->
            <template
                v-if="!initialized"
                v-slot:table-busy
            >
                <div class="text-center my-2">
                    <font-awesome-icon
                        icon="spinner"
                        spin
                    />
                    {{ $t('app.loading') }}
                </div>
            </template>

            <!-- Name column -->
            <template v-slot:cell(name)="data">
                <a
                    href="javascript:;"
                    @click.prevent="$emit('selectRole', data.item)"
                >{{ data.value }}</a>
            </template>

        </b-table>

        <p>
            <!-- Refresh button -->
            <b-button
                v-if="initialized"
                variant="primary"
                size="sm"
                :disabled="isBusy"
                @click="refresh"
            >
                <font-awesome-icon
                    icon="sync"
                    :spin="isBusy"
                />
            </b-button>
        </p>
    </div>
</template>

<script>
import moment from 'moment'
import { BTable } from 'bootstrap-vue'
export default {
    components: {
        BTable,
    },
    props: {
        apiUrl: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            fields: [
                {
                    key: 'name',
                    label: this.$t('app.name')
                },
                {
                    key: 'users_count',
                    label: this.$t('app.users'),
                    class: 'text-right fit',
                    formatter: (value, key, item) => item.relationships.users.total
                },
                {
                    key: 'admins_count',
                    label: this.$t('app.role_administrators'),
                    class: 'text-right fit d-none d-sm-table-cell',
                    formatter: (value, key, item) => item.relationships.administrators.total
                },
                {
                    key: 'created_at',
                    label: this.$t('app.created'),
                    class: 'fit d-none d-md-table-cell',
                    formatter: (value, key, item) => moment(value).fromNow()
                }
            ],
            isBusy: false,
            initialized: false
        }
    },
    methods: {
        refresh() {
            this.$refs.table.refresh()
        },
        itemProvider(ctx) {
            this.isBusy = true
            return axios.get(`${ctx.apiUrl}`)
                .then(res => {
                    const items = res.data.data
                    this.isBusy = false
                    this.initialized = true
                    return items || []
                })
                .catch(err => {
                    handleAjaxError(err)
                    this.isBusy = false
                    return []
                })
        }
    }
}
</script>