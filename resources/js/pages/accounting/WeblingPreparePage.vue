<template>
    <div v-if="period">

        <p>
            {{ $t('accounting.the_following_transactions_in_period_can_be_booked', {
                from: toDateString(from),
                to: toDateString(to),
                title: period.title
            }) }}
        </p>
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
                        >
                            <b-td class="fit">
                                <a
                                    :href="route('accounting.transactions.show', transaction.id)"
                                    target="_blank"
                                    :title="$t('app.open_in_new_window')"
                                >
                                    {{ transaction.date }}
                                </a>
                            </b-td>
                            <b-td class="text-success text-right fit">
                                <template v-if="transaction.type == 'income'">
                                    {{ numberFormat(transaction.amount) }}
                                </template>
                            </b-td>
                            <b-td class="text-danger text-right fit">
                                <template v-if="transaction.type == 'spending'">
                                    {{ numberFormat(transaction.amount) }}
                                </template>
                            </b-td>
                            <b-td>
                                <b-form-input
                                     :name="`posting_text[${transaction.id}]`"
                                     :value="postingText(transaction)"
                                     :placeholder="$t('accounting.posting_text')"
                                />
                            </b-td>
                            <b-td style="max-width: 8em">
                                <b-form-select
                                    v-model="selected"
                                    :options="transaction.type == 'income' ? assetsSelect : expenseSelect"
                                    :name="`debit_side[${transaction.id}]`"
                                >
                                    <template v-slot:first>
                                        <b-form-select-option :value="null">
                                            - {{ transaction.type == 'income' ? $t('accounting.money_to') : $t('accounting.paid_for') }} -
                                        </b-form-select-option>
                                    </template>
                                </b-form-select>
                            </b-td>
                            <b-td style="max-width: 8em">
                                <b-form-select
                                    v-model="selected"
                                    :options="transaction.type == 'income' ? incomeSelect : assetsSelect"
                                    :name="`credit_side[${transaction.id}]`"
                                >
                                    <template v-slot:first>
                                        <b-form-select-option :value="null">
                                            - {{ transaction.type == 'income' ? $t('accounting.received_for') : $t('accounting.paid_from') }} -
                                        </b-form-select-option>
                                    </template>
                                </b-form-select>
                            </b-td>
                            <b-td class="fit">
                                {{ transaction.receipt_no }}
                            </b-td>
                            <b-td class="fit">
                                <b-form-radio-group
                                    :options="action"
                                    :Value="defaultAction"
                                    stacked
                                    :name="`action[${transaction.id}]`"
                                />
                            </b-td>
                        </b-tr>
                    </b-tbody>
                </b-table-simple>
                <p>
                    <b-button
                        type="submit"
                        variant="primary"
                        :disabled="isBusy"
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
            actions: [
                {
                    value: 'ignore',
                    text: this.$t('app.ignore')
                },
                {
                    value: 'book',
                    text: this.$t('app.book')
                }
            ],
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
                this.transactions = data.transactions
                this.assetsSelect = data.assetsSelect.map(e => {

                })
                this.expenseSelect = data.expenseSelect.map(e => ({

                }))
                this.incomeSelect = data.incomeSelect.map(e => ({

                }))
            } catch (err) {
                alert(err)
            }
        },
        toDateString(value) {
            return moment(value).format(moment.HTML5_FMT.DATE)
        },
        onSubmit() {
            // route('accounting.webling.store')
            // {{ Form::hidden('period', $period->id) }}
            // {{ Form::hidden('from', $from->toDateString()) }}
            // {{ Form::hidden('to', $to->toDateString()) }}
        },
        numberFormat (value) {
            return numeral(value).format('0,0.00')
        },
        postingText (transaction) {
            let posting_text = `${transaction.category} - `
            if (transaction.project) {
                posting_text += `${transaction.project} - `
            }
            posting_text += transaction.description
            return posting_text
        }
    }
}
</script>
