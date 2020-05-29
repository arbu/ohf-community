<template>
    <div>
        <alert-with-retry
            :value="errorText"
            @retry="refresh"
        />

        <div class="d-flex justify-content-between align-items-center">

            <!-- Wallet selection -->
            <div class="mb-3">
                <template v-if="wallet">
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
                    v-if="classifications"
                    :value="filter"
                    :fixed_categories="classifications.fixed_categories"
                    :categories="classifications.categories"
                    :fixed_secondary_categories="classifications.fixed_secondary_categories"
                    :secondary_categories="classifications.secondary_categories"
                    :fixed_projects="classifications.fixed_projects"
                    :projects="classifications.projects"
                    :fixed_locations="classifications.fixed_locations"
                    :locations="classifications.locations"
                    :fixed_cost_centers="classifications.fixed_cost_centers"
                    :cost_centers="classifications.cost_centers"
                    :beneficiaries="classifications.beneficiaries"
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
            :items="items"
            :empty-text="$t('accounting.no_transactions_found')"
            :empty-filtered-text="$t('accounting.no_transactions_found')"
            :busy="isBusy"
            show-empty
            no-sort-reset
            no-footer-sorting
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :sort-null-last="true"
            no-local-sorting
        >
            <div slot="table-busy" class="text-center my-2">
                <b-spinner class="align-middle"></b-spinner>
                <strong>{{ $t('app.loading') }}</strong>
            </div>

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
                        v-if="busyItems.indexOf(data.item.id) >= 0"
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
        <table-pagination
            v-model="currentPage"
            :total-rows="totalRows"
            :per-page="perPage"
            :disabled="isBusy"
        />
    </div>
</template>

<script>
import numeral from 'numeral'
import moment from 'moment'
import { showSnackbar } from '@/utils'
import transactionsApi from '@/api/accounting/transactions'
import FilterFormModal from '@/components/accounting/FilterFormModal'
import AlertWithRetry from '@/components/alerts/AlertWithRetry'
import TablePagination from '@/components/table/TablePagination'
export default {
    components: {
        FilterFormModal,
        AlertWithRetry,
        TablePagination
    },
    data () {
        return {
            wallet: null,
            has_multiple_wallets: null,
            filter: {}, // TODO cache accounting.filter
            selectedTransaction: null,
            isBusy: true,
            items: null,
            busyItems: [],
            sortBy: 'created_at', // TODO cache accounting.sortColumn
            sortDesc: true, // TODO cache accounting.sortOrder
            classifications: null,
            errorText: null,
            currentPage: 1,
            perPage: 10,
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
    watch: {
        filter () {
            this.refreshItems()
        },
        currentPage () {
            this.refreshItems()
        },
        sortBy () {
            this.refreshItems()
        },
        sortDesc () {
            this.refreshItems()
        }
    },
    async created () {
        await this.fetchClassifications()
        this.refreshItems()
    },
    methods: {
        async refreshItems () {
            this.isBusy = true
            this.items = await this.fetchData({
                filter: this.filter,
                currentPage: this.currentPage,
                perPage: this.perPage,
                sortBy: this.sortBy,
                sortDesc: this.sortDesc
            })
            this.isBusy = false
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
                this.wallet = data.meta.wallet
                this.has_multiple_wallets = data.meta.has_multiple_wallets
                this.totalRows = data.meta.total
                return data.data
            } catch (err) {
                this.errorText = err
                return []
            }
        },
        async fetchClassifications () {
            this.classifications = await transactionsApi.filterClassifications()
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
        chooseReceiptForUpload (item) {
            this.selectedItemId = item.id
            this.$refs.fileUpload.click()
        },
        onFileSelect (evt) {
            if (evt.target.files.length > 0) {
                this.uploadReceiptPicture(this.selectedItemId, evt.target.files[0])
            }
        },
        async uploadReceiptPicture(itemId, file) {
            this.busyItems.push(itemId)
            try {
                let data = await transactionsApi.updateReceiptPicture(itemId, file)
                showSnackbar(data.message)
                let idx = this.items.findIndex(e => e.id == itemId)
                this.items[idx].receipt_pictures = data.receipt_pictures
                this.$refs.table.refresh()
            } catch (err) {
                alert(err)
            }
            this.busyItems = this.busyItems.filter(e => e != itemId)
        },
        applyFilter (bvModalEvt, data) {
            this.filter = { ...data }
            // TODO update chached value
        },
        resetFilter () {
            this.filter = {}
            // TODO remove cached value
        },
        refresh () {
            this.refreshItems()
        }
    }
}
</script>
