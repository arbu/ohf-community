<template>
    <b-table
        :fields="fields"
        :items="fetchData"
        small
        bordered
        striped
        hover
        responsive
        :empty-text="$t('accounting.no_wallets_found')"
        :busy.sync="isBusy"
        show-empty
    >
        <!-- Busy  -->
        <div slot="table-busy" class="text-center my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>{{ $t('app.loading') }}</strong>
        </div>

        <!-- Name -->
        <template v-slot:cell(name)="data">
            <b-link
                :href="data.item.can_update ? route('accounting.wallets.edit', data.item.id) : null"
            >
                {{ data.value }}
            </b-link>
        </template>

        <!-- Name -->
        <template v-slot:cell(num_transactions)="data">
            <b-link
                :href="data.item.can_update ? route('accounting.wallets.transactions.index', data.item.id) : null"
            >
                {{ data.value }}
            </b-link>
        </template>

        <!-- Is default -->
        <template v-slot:cell(is_default)="data">
            <font-awesome-icon :icon="data.value ? 'check' : 'times'" />
        </template>

        <!-- Is restricted -->
        <template v-slot:cell(is_restricted)="data">
            <font-awesome-icon :icon="data.item.roles.length > 0 ? 'check' : 'times'" />
        </template>

    </b-table>
</template>

<script>
import numeral from 'numeral'
import moment from 'moment'
import walletApi from '@/api/accounting/wallets'
export default {
    data () {
        return {
            isBusy: false,
            fields: [
                {
                    key: 'name',
                    label: this.$t('app.name')
                },
                {
                    key: 'amount',
                    label: this.$t('app.amount'),
                    class: 'text-right',
                    formatter: (value) => numeral(value).format('0,0.00')
                },
                {
                    key: 'num_transactions',
                    label: this.$t('accounting.transactions'),
                    class: 'text-right',
                    formatter: (value) => numeral(value).format('0,0')
                },
                {
                    key: 'is_default',
                    label: this.$t('app.default'),
                    class: 'fit text-center'
                },
                {
                    key: 'is_restricted',
                    label: this.$t('app.restricted'),
                    class: 'fit text-center',
                },
                {
                    key: 'latest_activity',
                    label: this.$t('app.latest_activity'),
                    class: 'fit',
                    formatter: (value) => value ? moment(value).format('LLL') : null
                },
                {
                    key: 'created_at',
                    label: this.$t('app.created'),
                    class: 'fit',
                    formatter: (value) => moment(value).format('LL')
                }
            ]
        }
    },
    methods: {
        async fetchData () {
            try {
                let data = await walletApi.list()
                return data.data || []
            } catch (err) {
                alert(err)
                return []
            }
        }
    }
}
</script>
