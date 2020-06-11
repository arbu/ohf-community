<template>
    <div v-if="loaded">
        <b-form-row>

            <!-- Heading -->
            <b-col sm>
                <h2 class="mb-4">
                    {{ $t('accounting.summary') }} {{ heading }}
                    <small>{{ wallet.name }}</small>
                </h2>
            </b-col>

            <!-- Month select -->
            <b-col
                v-if="months.length > 0"
                sm="auto"
            >
                <b-form-select
                    v-model="currentYearMonth"
                    :options="monthOptions"
                    class="mb-2"
                    @change="changeMonth"
                >
                    <template v-slot:first>
                        <b-form-select-option :value="null">- {{ $t('app.by_month') }} -</b-form-select-option>
                    </template>
                </b-form-select>
            </b-col>

            <!-- Year select -->
            <b-col
                v-if="years.length > 0"
                sm="auto"
            >
                <b-form-select
                    v-model="currentYear"
                    :options="yearOptions"
                    class="mb-2"
                    @change="changeYear"
                >
                    <template v-slot:first>
                        <b-form-select-option :value="null">- {{ $t('app.by_year') }} -</b-form-select-option>
                    </template>
                </b-form-select>
            </b-col>
        </b-form-row>

        <b-row>

            <!-- Revenue by categories -->
            <b-col sm>
                <b-card
                    :header="$t('app.categories')"
                    class="mb-4"
                    :no-body="revenueByCategory.length > 0"
                >
                    <b-list-group
                        v-if="revenueByCategory.length > 0"
                        flush
                    >
                        <b-list-group-item
                            v-for="v in revenueByCategory"
                            :key="v.name"
                            :href="indexFilterUrl('category', v.name)"
                            class="d-flex justify-content-between"
                        >
                            <span>{{ v.name }}</span>
                            <span :class="v.amount > 0 ? 'text-success' : 'text-danger'">
                                {{ numberFormat(v.amount) }}
                            </span>
                        </b-list-group-item>
                    </b-list-group>
                    <template v-else>
                        <em>{{ $t('app.no_data_available_in_the_selected_time_range') }}</em>
                    </template>
                </b-card>
            </b-col>

            <!-- Revenue by project -->
            <b-col sm>
                <b-card
                    :header="$t('app.projects')"
                    class="mb-4"
                    :no-body="revenueByProject.length > 0"
                >
                    <b-list-group
                        v-if="revenueByProject.length > 0"
                        flush
                    >
                        <b-list-group-item
                            v-for="v in revenueByProject"
                            :key="v.name"
                            :href="indexFilterUrl('project', v.name)"
                            class="d-flex justify-content-between"
                        >
                            <span v-if="v.name">{{ v.name }}</span>
                            <em v-else>{{ $t('app.no_project') }}</em>
                            <span :class="v.amount > 0 ? 'text-success' : 'text-danger'">
                                {{ numberFormat(v.amount) }}
                            </span>
                        </b-list-group-item>
                    </b-list-group>
                    <template v-else>
                        <em>{{ $t('app.no_data_available_in_the_selected_time_range') }}</em>
                    </template>
                </b-card>
            </b-col>

            <!-- Wallet -->
            <b-col sm>
                <b-card
                    :header="$t('app.total')"
                    class="mb-4"
                    no-body
                >
                    <b-table-simple
                        class="mb-0"
                    >
                        <b-tbody>
                            <b-tr>
                                <b-td>{{ $t('accounting.income') }}</b-td>
                                <b-td class="text-right">
                                    <u>{{ numberFormat(income) }}</u>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>{{ $t('accounting.spending') }}</b-td>
                                <b-td class="text-right">
                                    <u>{{ numberFormat(spending) }}</u>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>{{ $t('accounting.difference') }}</b-td>
                                <b-td class="text-right">
                                    <u>{{ numberFormat(income - spending) }}</u>
                                </b-td>
                            </b-tr>
                            <b-tr>
                                <b-td>{{ $t('accounting.wallet') }}</b-td>
                                <b-td
                                    class="text-right"
                                    :class="wallet_amount < 0 ? 'text-danger' : null"
                                >
                                    <u>{{ numberFormat(wallet_amount) }}</u>
                                </b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </b-card>
            </b-col>
        </b-row>

    </div>
    <p v-else>
        {{ $t('app.loading') }}
    </p>
</template>

<script>
import transactionsApi from '@/api/accounting/transactions'
import numeral from 'numeral'
import moment from 'moment'
export default {
    props: {
        walletId: {
            required: true
        }
    },
    data () {
        return {
            loaded: false,
            wallet: {},
            months: [],
            years: {},
            currentYearMonth: null,
            currentYear: null,
            revenueByCategory: [],
            revenueByProject: [],
            can_view_transactions: false,
            income: 0,
            spending: 0,
            wallet_amount: 0
        }
    },
    computed: {
        heading () {
            if (this.currentYearMonth !== null) {
                return moment(this.currentYearMonth).format('MMMM YYYY')
            }
            if (this.currentYear !== null) {
                return this.currentYear
            }
            return this.$t('app.all_time')
        },
        monthOptions () {
            return this.months.map(e => ({
                value: e,
                text: moment(e).format('MMMM YYYY'),
            }))
        },
        yearOptions () {
            return this.years.map(e => ({
                value: e,
                text: e,
            }))
        },
        filterDateStart () {
            if (this.currentYearMonth !== null) {
                return moment(this.currentYearMonth).startOf('month').format(moment.HTML5_FMT.DATE)
            }
            if (this.currentYear !== null) {
                return moment(this.currentYear).startOf('year').format(moment.HTML5_FMT.DATE)
            }
            return null;
        },
        filterDateEnd () {
            if (this.currentYearMonth !== null) {
                return moment(this.currentYearMonth).endOf('month').format(moment.HTML5_FMT.DATE)
            }
            if (this.currentYear !== null) {
                return moment(this.currentYear).endOf('year').format(moment.HTML5_FMT.DATE)
            }
            return null;
        }
    },
    mounted () {
        let params = {}
        if (sessionStorage.getItem('accounting.summary_range.year')) {
            let val = parseInt(sessionStorage.getItem('accounting.summary_range.year'))
            params.year = val > 0 ? val : ''
        }
        if (sessionStorage.getItem('accounting.summary_range.month')) {
            let val = parseInt(sessionStorage.getItem('accounting.summary_range.month'))
            params.month = val > 0 ? val : ''
        }
        this.fetchData(params)
    },
    methods: {
        async fetchData (params = {}) {
            try {
                let data = await transactionsApi.fetchSummary(this.walletId, params)
                this.wallet = data.wallet
                this.months = data.months
                this.years = data.years
                this.currentYearMonth = data.current_year_month
                this.currentYear = data.current_year
                this.revenueByCategory = data.revenue_by_category
                this.revenueByProject = data.revenue_by_project
                this.can_view_transactions = data.can_view_transactions
                this.income = data.income
                this.spending = data.spending
                this.wallet_amount = data.wallet_amount
                this.loaded = true
            } catch (err) {
                alert(err)
            }
        },
        numberFormat (value) {
            return numeral(value).format('0,0.00')
        },
        changeMonth () {
            let month = ''
            let year = ''
            if (this.currentYearMonth !== null) {
                var arr = this.currentYearMonth.split('-');
                month = parseInt(arr[1]);
                year = arr[0]
            }
            this.fetchData({
                month: month,
                year: year
            })
            this.rememberState(year, month)
        },
        changeYear () {
            let year = ''
            if (this.currentYear !== null) {
                year = this.currentYear
            }
            this.fetchData({
                year: year
            })
            this.rememberState(year, '')
        },
        rememberState (year, month) {
            sessionStorage.setItem('accounting.summary_range.year', year)
            sessionStorage.setItem('accounting.summary_range.month', month)
        },
        indexFilterUrl (key, value) {
            if (this.can_view_transactions) {
                return this.route('accounting.wallets.transactions.index', {
                    wallet: this.walletId,
                    filter: {
                        [key]: value !== null ? value : '',
                        date_start: this.filterDateStart,
                        date_end: this.filterDateEnd
                    }
                })
            }
            return null
        }
    }
}
</script>
