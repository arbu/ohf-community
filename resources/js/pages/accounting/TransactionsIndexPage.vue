<template>
    <div v-if="wallet">

        <!-- Alert  -->
        <alert-with-retry
            :value="errorText"
            @retry="refresh"
        />

        <div class="d-flex justify-content-between align-items-center">

            <!-- Wallet selection -->
            <div class="mb-3">
                <template>
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
                </template>
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
                    @submit="applyFilter"
                    @reset="resetFilter"
                />
            </div>

        </div>

        <!-- Table -->
        <b-table
            ref="table"
            :id="id"
            small
            bordered
            striped
            hover
            responsive
            :fields="fields"
            :items="fetchData"
            :empty-text="$t('accounting.no_transactions_found')"
            :empty-filtered-text="$t('accounting.no_transactions_found')"
            :busy.sync="isBusy"
            :filter="filter"
            :currentPage="currentPage"
            :per-page="perPage"
            show-empty
            no-sort-reset
            no-footer-sorting
            :sort-by="sortBy"
            :sort-desc="sortDesc"
        >
            <!-- Busy  -->
            <div slot="table-busy" class="text-center my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>{{ $t('app.loading') }}</strong>
            </div>

            <!-- Receipt picture column -->
            <template v-slot:cell(receipt_pictures)="data">
                <receipt-picture-button
                    :id="data.item.id"
                    :value="data.value"
                />
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
                <template v-if="!isBusy && isFilterActive && (sum_income > 0 || sum_spending > 0)">
                    <b-tr>
                        <b-td colspan="3" rowspan="2" class="align-middle">
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
                        <b-td :colspan="data.columns - 6" rowspan="2"></b-td>
                    </b-tr>
                    <b-tr class="d-none d-sm-table-row">
                        <b-td colspan="2" class="text-center">
                            <u>{{ numberFormat(sum_income - sum_spending) }}</u>
                        </b-td>
                    </b-tr>
                </template>
            </template>
        </b-table>

        <!-- Pagination -->
        <table-pagination
            v-model="currentPage"
            :total-rows="totalRows"
            :per-page="perPage"
            :disabled="isBusy"
        />

    </div>
    <p v-else>
        {{ $t('app.loading') }}
    </p>
</template>

<script>
import numeral from 'numeral'
import moment from 'moment'
import transactionsApi from '@/api/accounting/transactions'
import FilterFormModal from '@/components/accounting/FilterFormModal'
import ReceiptPictureButton from '@/components/accounting/ReceiptPictureButton'
import AlertWithRetry from '@/components/alerts/AlertWithRetry'
import TablePagination from '@/components/table/TablePagination'
export default {
    components: {
        FilterFormModal,
        ReceiptPictureButton,
        AlertWithRetry,
        TablePagination
    },
    data () {
        const id = 'accountingTransactionsTable'
        return {
            id: id,
            wallet: null,
            has_multiple_wallets: null,
            use_secondary_categories: false,
            use_locations: false,
            use_cost_centers: false,
            isBusy: false,
            filter: sessionStorage.getItem(id + '.filter')
                ? JSON.parse(sessionStorage.getItem(id + '.filter'))
                : {},
            sortBy: sessionStorage.getItem(id + '.sortBy')
                ? sessionStorage.getItem(id + '.sortBy')
                :'created_at',
            sortDesc: sessionStorage.getItem(id + '.sortDesc')
                ? sessionStorage.getItem(id + '.sortDesc') == 'true'
                : true,
            currentPage: sessionStorage.getItem(id + '.currentPage')
                ? parseInt(sessionStorage.getItem(id + '.currentPage'))
                : 1,
            errorText: null,
            perPage: 100,
            totalRows: 0,
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
                    class: !this.use_secondary_categories ? 'd-none' : null,
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
                    class: !this.use_locations ? 'd-none' : null,
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'cost_center',
                    label: this.$t('accounting.cost_center'),
                    class: !this.use_cost_centers ? 'd-none' : null,
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
    async created () {
        this.fetchWallet()
    },
    methods: {
        async fetchWallet () {
            let data = await transactionsApi.fetchCurrentWallet()
            this.wallet = data.data
            this.has_multiple_wallets = data.meta.has_multiple_wallets
            this.use_secondary_categories = data.meta.use_secondary_categories
            this.use_locations = data.meta.use_locations
            this.use_cost_centers = data.meta.use_cost_centers
        },
        async fetchData (ctx) {
            try {
                const params = {
                    filter: ctx.filter,
                    page: ctx.currentPage,
                    pageSize: ctx.perPage,
                    sortBy: ctx.sortBy,
                    sortDirection: ctx.sortDesc ? 'desc' : 'asc'
                }
                let data = await transactionsApi.list(params)

                this.sum_income = data.meta.sum_income
                this.sum_spending = data.meta.sum_spending
                this.totalRows = data.meta.total

                // Cache parameters
                sessionStorage.setItem(this.id + '.sortBy', ctx.sortBy)
                sessionStorage.setItem(this.id + '.sortDesc', ctx.sortDesc)
                sessionStorage.setItem(this.id + '.currentPage', ctx.currentPage)
                sessionStorage.setItem(this.id + '.filter', JSON.stringify(ctx.filter))

                return data.data
            } catch (err) {
                this.errorText = err

                // Reset cached parameters
                sessionStorage.removeItem(this.id + '.sortBy')
                sessionStorage.removeItem(this.id + '.sortDesc')
                sessionStorage.removeItem(this.id + '.currentPage')
                sessionStorage.removeItem(this.id + '.filter')

                return []
            }
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
        applyFilter (bvModalEvt, data) {
            this.filter = { ...data }
        },
        resetFilter () {
            this.filter = {}
        },
        refresh () {
            this.$refs.table.refresh()
        }
    }
}
</script>
