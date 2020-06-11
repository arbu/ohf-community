<template>
    <transaction-form
        v-if="newReceiptNumber"
        :new-receipt-number="newReceiptNumber"
        :disabled="isBusy"
        @submit="storeItem"
    />
    <p v-else>
        {{ $t('app.loading') }}
    </p>
</template>

<script>
import walletsApi from '@/api/accounting/wallets'
import transactionsApi from '@/api/accounting/transactions'
import TransactionForm from '@/components/accounting/TransactionForm'
import showSnackbar from '@/snackbar'
export default {
    components: {
        TransactionForm
    },
    props: {
        walletId: {
            required: true
        }
    },
    data () {
        return {
            isBusy: false,
            newReceiptNumber: null
        }
    },
    async created () {
        this.fetchWallet()
    },
    methods: {
        async fetchWallet () {
            let data = await walletsApi.find(this.walletId)
            this.newReceiptNumber = data.data.next_free_receipt_number
        },
        async storeItem (formData) {
            this.isBusy = true
            try {
                let data = await transactionsApi.store(this.walletId, formData)
                showSnackbar(data.message)
                window.document.location = this.route('accounting.wallets.transactions.index', this.walletId)
            } catch (err) {
                alert(err)
            }
            this.isBusy = false
        }
    }
}
</script>
