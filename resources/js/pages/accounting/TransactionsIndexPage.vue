<template>
    <div v-if="loaded">

        <div class="d-flex justify-content-between align-items-center">

            <!-- Wallet selection -->
            <div class="mb-3">
                <font-awesome-icon icon="wallet" />
                <span class="d-none d-sm-inline">
                    <template v-if="has_multiple_wallets">
                        {{ wallet.name }}:
                    </template>
                    <template v-else>
                        {{ $t('accounting.wallet') }}:
                    </template>
                </span>
                <u>{{ numberFormat(wallet.amount) }}</u>
                <template v-if="has_multiple_wallets">
                    <a
                        :href="route('accounting.wallets.change')"
                        class="d-none d-sm-inline"
                    >
                        {{ $t('app.change') }}
                    </a>
                    <a
                        :href="route('accounting.wallets.change')"
                        class="d-inline d-sm-none btn btn-sm"
                    >
                        <font-awesome-icon icon="folder-open" />
                    </a>
                </template>
            </div>

            <!-- Filter -->
            <div class="text-right">
                <filter-form-modal
                    :value="filter"
                    :fixed_categories="fixed_categories"
                    :categories="categories"
                    :fixed_secondary_categories="fixed_secondary_categories"
                    :secondary_categories="secondary_categories"
                    :fixed_projects="fixed_projects"
                    :projects="projects"
                    :fixed_locations="fixed_locations"
                    :locations="locations"
                    :fixed_cost_centers="fixed_cost_centers"
                    :cost_centers="cost_centers"
                    :beneficiaries="beneficiaries"
                    @submit="applyFilter"
                    @reset="resetFilter"
                />
            </div>

        </div>

        <!-- Receipt picture upload input -->
        <input
            ref="fileUpload"
            type="file"
            accept="image/*"
            class="d-none"
            @change="onFileSelect"
        />

        <!-- Table -->
        <b-table
            ref="table"
            small
            bordered
            striped
            hover
            responsive
            :fields="fields"
            :items="transactions"
            :empty-text="$t('accounting.no_transactions_found')"
            no-sort-reset
            no-footer-sorting
            sort-by="created_at"
            :sort-desc="true"
            :sort-null-last="true"
        >
            <!-- Receipt picture column -->
            <template v-slot:cell(receipt_pictures)="data">
                <template v-if="data.value">
                    <b-button
                        :href="data.value[0]"
                        size="sm"
                        variant="secondary"
                        data-lity
                    >
                        <font-awesome-icon icon="image" />
                    </b-button>
                </template>
                <template v-else>
                    <b-button
                        v-if="busyTransactions.indexOf(data.item.id) >= 0"
                        disabled
                        size="sm"
                        variant="light"
                    >
                        <font-awesome-icon icon="spinner" spin />
                    </b-button>
                    <b-button
                        v-else
                        size="sm"
                        variant="warning"
                        @click="chooseReceiptForUpload(data.item)"
                    >
                        <font-awesome-icon icon="plus-circle" />
                    </b-button>
                </template>
            </template>

            <!-- Date column -->
            <template v-slot:cell(date)="data">
                <a
                    :href="route('accounting.transactions.show', data.item.id)"
                >
                    {{ dateFormat(data.value) }}
                </a>
            </template>

            <!-- Amount column -->
            <template v-slot:cell(amount)="data">
                <span :class="{ 'text-success': data.item.type == 'income', 'text-danger': data.item.type == 'spending' }">
                    {{ numberFormat(data.value) }}
                </span>
            </template>

            <!-- Income column -->
            <template v-slot:cell(income)="data">
                <span
                    v-if="data.item.type == 'income'"
                    class="text-success"
                >
                    {{ numberFormat(data.item.amount) }}
                </span>
            </template>

            <!-- Spending column -->
            <template v-slot:cell(spending)="data">
                <span
                    v-if="data.item.type == 'spending'"
                    class="text-danger"
                >
                    {{ numberFormat(data.item.amount) }}
                </span>
            </template>

            <!-- Create at column -->
            <template v-slot:cell(created_at)="data">
                {{ dateTimeFormat(data.value) }}
                <template v-if="data.item.audit_user_name">
                    <small class="text-muted ml-1">{{ data.item.audit_user_name }}</small>
                </template>
            </template>

            <!-- Footer -->
            <template v-slot:custom-foot="data">
                <template v-if="isFilterActive && (sum_income > 0 || sum_spending > 0)">
                    <b-tr>
                        <b-td colspan="2" rowspan="2" class="align-middle">
                            {{ $t('app.total') }}
                        </b-td>
                        <b-td class="text-right d-none d-sm-table-cell">
                            <u class="text-success">
                                {{ numberFormat(sum_income) }}
                            </u>
                        </b-td>
                        <b-td class="text-right d-none d-sm-table-cell">
                            <u class="text-danger">
                                {{ numberFormat(sum_spending) }}
                            </u>
                        </b-td>
                        <b-td class="text-right d-table-cell d-sm-none">
                            <template v-if="sum_income > 0"><u class="text-success">{{ numberFormat(sum_income) }}</u><br></template>
                            <template v-if="sum_spending > 0"><u class="text-danger">{{ numberFormat(sum_spending) }}</u></template>
                            <u>{{ numberFormat(sum_income - sum_spending) }}</u>
                        </b-td>
                        <b-td :colspan="data.columns - 5" rowspan="2"></b-td>
                    </b-tr>
                    <b-tr class="d-none d-sm-table-row">
                        <b-td colspan="2" class="text-center">
                            <u>{{ numberFormat(sum_income - sum_spending) }}</u>
                        </b-td>
                    </b-tr>
                </template>
            </template>
        </b-table>
        <!-- TODO
        <div style="overflow-x: auto">
            {{ $transactions->appends($filter)->links() }}
        </div>
        -->

    </div>
    <p v-else>
        {{ $t('app.loading') }}
    </p>
</template>

<script>
import numeral from 'numeral'
import moment from 'moment'
import { showSnackbar } from '@/utils'
import transactionsApi from '@/api/accounting/transactions'
import FilterFormModal from '@/components/accounting/FilterFormModal'
export default {
    components: {
        FilterFormModal
    },
    data () {
        return {
            loaded: false,
            wallet: null,
            has_multiple_wallets: null,
            filter: {},
            transactions: null,
            selectedTransaction: null,
            busyTransactions: []
        }
    },
    computed: {
        fields () {
            return [
                {
                    key: 'receipt_pictures',
                    label: '',
                    headerTitle: this.$t('accounting.receipt'),
                    class: 'fit text-center',
                    tdClass: 'align-middle'
                },
                {
                    key: 'receipt_no',
                    label: this.$t('accounting.receipt'),
                    class: 'fit text-center',
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'date',
                    label: this.$t('app.date'),
                    class: 'fit',
                    tdClass: 'align-middle',
                    sortable: true,
                    sortDirection: 'desc'
                },
                {
                    key: 'amount',
                    label: this.$t('app.amount'),
                    class: 'fit d-table-cell d-sm-none text-right',
                    tdClass: 'align-middle'
                },
                {
                    key: 'income',
                    label: this.$t('accounting.income'),
                    class: 'fit d-none d-sm-table-cell text-right',
                    tdClass: 'align-middle'
                },
                {
                    key: 'spending',
                    label: this.$t('accounting.spending'),
                    class: 'fit d-none d-sm-table-cell text-right',
                    tdClass: 'align-middle'
                },
                {
                    key: 'category',
                    label: this.$t('app.category'),
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'secondary_category',
                    label: this.$t('app.secondary_category'),
                    class: this.secondary_categories == null ? 'd-none' : null,
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'project',
                    label: this.$t('app.project'),
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'location',
                    label: this.$t('app.location'),
                    class: this.locations == null ? 'd-none' : null,
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'cost_center',
                    label: this.$t('accounting.cost_center'),
                    class: this.cost_centers == null ? 'd-none' : null,
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'description',
                    label: this.$t('app.description'),
                    class: 'd-none d-sm-table-cell',
                    tdClass: 'align-middle'
                },
                {
                    key: 'beneficiary',
                    label: this.$t('accounting.beneficiary'),
                    class: 'd-none d-sm-table-cell',
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'created_at',
                    label: this.$t('app.registered'),
                    class: 'fit d-none d-md-table-cell',
                    tdClass: 'align-middle',
                    sortable: true,
                    sortDirection: 'desc'
                }
            ]
        },
        isFilterActive () {
            return Object.values(this.filter)
                .filter(e => e != null && e != '')
                .length > 0
        }
    },
    created () {
        this.fetchData()
    },
    methods: {
        async fetchData () {
            let data = await transactionsApi.list()

            this.wallet = data.wallet
            this.has_multiple_wallets = data.has_multiple_wallets
            this.filter = data.filter
            this.transactions = data.transactions.data
            this.sum_income = data.sum_income
            this.sum_spending = data.sum_spending
            this.fixed_categories = data.fixed_categories
            this.categories = data.categories
            this.fixed_secondary_categories = data.fixed_secondary_categories
            this.secondary_categories = data.secondary_categories
            this.fixed_projects = data.fixed_projects
            this.projects = data.projects
            this.fixed_locations = data.fixed_locations
            this.locations = data.locations
            this.fixed_cost_centers = data.fixed_cost_centers
            this.cost_centers = data.cost_centers
            this.beneficiaries = data.beneficiaries

            this.loaded = true
        },
        numberFormat (value) {
            return numeral(value).format('0,0.00')
        },
        dateFormat (value) {
            return moment(value).format('LL')
        },
        dateTimeFormat (value) {
            return moment(value).format('LLL')
        },
        chooseReceiptForUpload (transaction) {
            this.selectedTransaction = transaction
            this.$refs.fileUpload.click()
        },
        onFileSelect (evt) {
            if (evt.target.files.length > 0) {
                this.uploadReceiptPicture(this.selectedTransaction, evt.target.files[0])
            }
        },
        async uploadReceiptPicture(transaction, file) {
            this.busyTransactions.push(transaction.id)
            try {
                let data = await transactionsApi.updateReceiptPicture(transaction.id, file)
                showSnackbar(data.message)
                let idx = this.transactions.findIndex(e => e.id == transaction.id)
                this.transactions[idx].receipt_pictures = data.receipt_pictures
                this.$refs.table.refresh()
            } catch (err) {
                alert(err)
            }
            this.busyTransactions = this.busyTransactions.filter(e => e != transaction.id)
        },
        async applyFilter (bvModalEvt, data) {
            alert(JSON.stringify(data))
            this.filter = { ...data }

            // Form::open(['route' => ['accounting.transactions.index' ], 'method' => 'get'])
            // bvModalEvt.preventDefault()
        },
        async resetFilter () {
            this.filter = {}
            // <!-- :href="`${route('accounting.transactions.index')}?reset_filter=1`" -->
        }
    }
}
</script>
