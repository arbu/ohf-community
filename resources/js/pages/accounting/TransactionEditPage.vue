<template>
    <alert-with-retry
        v-if="errorText"
        :value="errorText"
        @retry="fethData"
    />
    <div v-else-if="transaction">
        <transaction-form
            :transaction="transaction"
            @submit="updateDonation"
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
import AlertWithRetry from '@/components/alerts/AlertWithRetry'
import TransactionForm from '@/components/accounting/TransactionForm'
import showSnackbar from '@/snackbar'
export default {
    components: {
        AlertWithRetry,
        TransactionForm
    },
    props: {
        id: {
            required: true
        }
    },
    data () {
        return {
            transaction: null,
            errorText: null,
            isBusy: false
        }
    },
    async created () {
        this.fethData()
    },
    methods: {
        async fethData () {
            try {
                let data = await transactionsApi.find(this.id)
                this.transaction = data.data
            } catch (err) {
                this.errorText = err
            }
        },
        async updateDonation (formData) {
            this.isBusy = true
            try {
                let data = await transactionsApi.update(this.id, formData)
                showSnackbar(data.message)
                window.document.location = this.route('accounting.transactions.show', this.id)
            } catch (err) {
                alert(err)
            }
            this.isBusy = false
        },
        numberFormat (value) {
            return numeral(value).format('0,0.00')
        },
        dateFormat (value) {
            return moment(value).format('LL')
        },
        dateTimeFormat (value) {
            return moment(value).format('LLL')
        }
    }
}
</script>
