<template>
    <div v-if="period">

        <p v-html="$t('accounting.the_following_transactions_in_period_can_be_booked', {
                from: toDateString(from),
                to: toDateString(to),
                title: period.title
        })"></p>
        <template v-if="transactions.length > 0">
            <b-form @submit.stop.prevent="onSubmit">
                <b-table-simple
                    id="bookings_table"
                    responsive
                    small
                    bordered
                    striped
                    hover
                >
                    <b-thead>
                        <b-tr>
                            <b-th class="fit">{{ $t('app.date') }}</b-th>
                            <b-th class="fit text-right">{{ $t('accounting.credit') }}</b-th>
                            <b-th class="fit text-right">{{ $t('accounting.debit') }}</b-th>
                            <b-th>{{ $t('accounting.posting_text') }}</b-th>
                            <b-th>{{ $t('accounting.debit_side') }}</b-th>
                            <b-th>{{ $t('accounting.credit_side') }}</b-th>
                            <b-th class="fit">{{ $t('accounting.receipt_no') }}</b-th>
                            <b-th class="fit">{{ $t('app.action') }}</b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody>
                        <b-tr
                            v-for="transaction in transactions"
                            :key="transaction.id"
                            :class="rowClass(transaction)"
                        >
                            <!-- Date -->
                            <b-td class="fit align-middle">
                                <a
                                    :href="route('accounting.transactions.show', transaction.id)"
                                    target="_blank"
                                    :title="$t('app.open_in_new_window')"
                                >
                                    {{ transaction.date }}
                                </a>
                            </b-td>
                            <!-- Amount -->
                            <b-td class="text-success text-right fit align-middle">
                                <template v-if="transaction.type == 'income'">
                                    {{ numberFormat(transaction.amount) }}
                                </template>
                            </b-td>
                            <b-td class="text-danger text-right fit align-middle">
                                <template v-if="transaction.type == 'spending'">
                                    {{ numberFormat(transaction.amount) }}
                                </template>
                            </b-td>
                            <!-- Posting test -->
                            <b-td class="align-middle">
                                <b-form-input
                                    v-model="transaction.posting_text"
                                    :placeholder="$t('accounting.posting_text')"
                                    trim
                                />
                            </b-td>
                            <!-- Debit side -->
                            <b-td
                                class="align-middle"
                                style="max-width: 8em"
                            >
                                <b-form-select
                                    :options="transaction.type == 'income' ? assetsSelect : expenseSelect"
                                    v-model="transaction.debit_side"
                                >
                                    <template v-slot:first>
                                        <b-form-select-option :value="null">
                                            - {{ transaction.type == 'income' ? $t('accounting.money_to') : $t('accounting.paid_for') }} -
                                        </b-form-select-option>
                                    </template>
                                </b-form-select>
                            </b-td>
                            <!-- Credit side -->
                            <b-td
                                class="align-middle"
                                style="max-width: 8em"
                            >
                                <b-form-select
                                    :options="transaction.type == 'income' ? incomeSelect : assetsSelect"
                                    v-model="transaction.credit_side"
                                >
                                    <template v-slot:first>
                                        <b-form-select-option :value="null">
                                            - {{ transaction.type == 'income' ? $t('accounting.received_for') : $t('accounting.paid_from') }} -
                                        </b-form-select-option>
                                    </template>
                                </b-form-select>
                            </b-td>
                            <!-- Receipt no -->
                            <b-td class="fit align-middle">
                                {{ transaction.receipt_no }}
                            </b-td>
                            <!-- Action -->
                            <b-td class="fit align-middle">
                                <b-form-radio
                                    v-model="transaction.action"
                                    value="ignore"
                                >
                                    {{ $t('app.ignore') }}
                                </b-form-radio>
                                <b-form-radio
                                    v-model="transaction.action"
                                    value="book"
                                    :disabled="!transaction.debit_side || !transaction.credit_side || !transaction.posting_text"
                                >
                                    {{ $t('accounting.book') }}
                                </b-form-radio>
                            </b-td>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>
                <p>
                    <b-button
                        type="submit"
                        variant="primary"
                        :disabled="isBusy || transactions.filter(t => t.action == 'book').length == 0"
                    >
                        <font-awesome-icon icon="check" />
                        {{ $t('app.submit') }}
                    </b-button>
                </p>
            </b-form>
        </template>
        <b-alert
            v-else
            show
            variant="info"
        >
            {{ $t('accounting.no_transactions_found') }}
        </b-alert>
    </div>
    <p v-else>
        {{ $t('app.loading') }}
    </p>
</template>

<script>
import moment from 'moment'
import numeral from 'numeral'
import weblingApi from '@/api/accounting/webling'
import showSnackbar from '@/snackbar'
export default {
    props: {
        periodId: {
            required: true
        },
        from: {
            required: true
        },
        to: {
            required: true
        }
    },
    data() {
        return {
            isBusy: false,
            period: null,
            transactions: [],
            assetsSelect: [],
            expenseSelect: [],
            incomeSelect: [],
            defaultAction: 'ignore',
        }
    },
    mounted () {
        this.fetchData()
    },
    methods: {
        async fetchData () {
            try {
                let data = await weblingApi.fetchPrepare(this.periodId, this.from, this.to)
                this.period = data.period
                this.transactions = data.transactions.map(t => {
                    t.posting_text = this.postingText(t)
                    t.debit_side = null
                    t.credit_side = null
                    t.action = this.defaultAction
                    return t
                })
                this.assetsSelect = Object.entries(data.assetsSelect).map(e => ({
                    label: e[0],
                    options: Object.entries(e[1]).map(f => ({
                        value: f[0],
                        text: f[1]
                    }))
                }))
                this.expenseSelect = Object.entries(data.expenseSelect).map(e => ({
                    label: e[0],
                    options: Object.entries(e[1]).map(f => ({
                        value: f[0],
                        text: f[1]
                    }))
                }))
                this.incomeSelect = Object.entries(data.incomeSelect).map(e => ({
                    label: e[0],
                    options: Object.entries(e[1]).map(f => ({
                        value: f[0],
                        text: f[1]
                    }))
                }))
            } catch (err) {
                alert(err)
            }
        },
        postingText (transaction) {
            let posting_text = `${transaction.category} - `
            if (transaction.project) {
                posting_text += `${transaction.project} - `
            }
            posting_text += transaction.description
            return posting_text
        },
        toDateString(value) {
            return moment(value).format(moment.HTML5_FMT.DATE)
        },
        numberFormat (value) {
            return numeral(value).format('0,0.00')
        },
        rowClass(transaction) {
            if (transaction.action == 'book') {
                if (transaction.posting_text != '' && transaction.debit_side != null && transaction.credit_side != null) {
                    return 'table-success'
                } else {
                    return 'table-warning'
                }
            } else {
                if (transaction.posting_text != '' && transaction.debit_side != null && transaction.credit_side != null) {
                    return 'table-secondary'
                }
            }
            return null
        },
        async onSubmit() {
            let selectedTransactions = this.transactions.filter(t => t.action == 'book'
                && t.debit_side != null
                && t.credit_side != null
                && t.posting_text.length > 0)
            if (selectedTransactions.length > 0) {
                let payload = selectedTransactions.map(t => ({
                    id: t.id,
                    action: t.action,
                    posting_text: t.posting_text,
                    debit_side: t.debit_side,
                    credit_side: t.credit_side
                }))
                try {
                    let data = await weblingApi.store(this.periodId, payload)
                    showSnackbar(data.message)
                    document.location.href = this.route('accounting.webling.index')
                } catch (err) {
                    alert(err)
                }
            } else {
                alert(this.$t('accounting.no_transactions_selected'))
            }
        }
    }
}
</script>
